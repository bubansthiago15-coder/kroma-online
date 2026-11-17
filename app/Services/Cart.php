<?php
declare(strict_types=1);

/**
 * Cart storage and locking. Anonymous carts live in the PHP session; signed-in
 * carts are stored in InnoDB and serialized per user through the usuario row.
 */
final class Cart
{
    private static ?array $itemsCache = null;

    public static function items(): array
    {
        self::mergeSessionCart();
        if (self::$itemsCache !== null) {
            return self::$itemsCache;
        }

        $userId = Auth::id();
        if ($userId === null) {
            return self::$itemsCache = self::sessionItems();
        }

        return self::$itemsCache = self::transactional(static function (PDO $db) use ($userId): array {
            $cartId = self::lockActiveCart($db, $userId, false);
            return $cartId === null ? [] : self::lockCartItems($db, $cartId);
        });
    }

    public static function quantity(int $productId): int
    {
        return (int) (self::items()[$productId] ?? 0);
    }

    public static function count(): int
    {
        return array_sum(self::items());
    }

    public static function add(int $productId, int $quantity): int
    {
        if ($productId < 1 || $quantity < 1) {
            throw new RuntimeException('Indicá un producto y una cantidad válidos.');
        }

        $userId = Auth::id();
        if ($userId === null) {
            $current = (int) (self::sessionItems()[$productId] ?? 0);
            $stock = self::transactional(static function (PDO $db) use ($productId): int {
                $product = self::lockProductStock($db, $productId);
                return (int) ($product['cantidad_disponible'] ?? 0);
            });
            self::assertStock($stock, $current, $quantity);
            $_SESSION['cart'][$productId] = $current + $quantity;
            self::invalidate();
            return $current + $quantity;
        }

        self::mergeSessionCart();
        $newQuantity = self::transactional(static function (PDO $db) use ($userId, $productId, $quantity): int {
            $cartId = self::lockActiveCart($db, $userId, true);
            $product = self::lockProductStock($db, $productId);
            $stock = (int) ($product['cantidad_disponible'] ?? 0);
            $current = self::cartQuantity($db, (int) $cartId, $productId);
            self::assertStock($stock, $current, $quantity);

            $next = $current + $quantity;
            if ($current > 0) {
                $stmt = $db->prepare('UPDATE detalle_carrito SET cantidad=? WHERE id_carrito=? AND id_producto=?');
                $stmt->execute([$next, $cartId, $productId]);
            } else {
                $stmt = $db->prepare('INSERT INTO detalle_carrito(id_carrito,id_producto,cantidad) VALUES(?,?,?)');
                $stmt->execute([$cartId, $productId, $next]);
            }
            self::touch($db, (int) $cartId);
            return $next;
        });

        self::invalidate();
        return $newQuantity;
    }

    /** Normalize submitted quantities against current stock. Returns true if changed. */
    public static function updateMany(array $quantities): bool
    {
        $userId = Auth::id();
        if ($userId === null) {
            $changed = false;
            $cart = self::sessionItems();
            $ids = array_map('intval', array_keys($quantities));
            sort($ids, SORT_NUMERIC);
            foreach ($ids as $productId) {
                if (!array_key_exists((string) $productId, $quantities) && !array_key_exists($productId, $quantities)) {
                    continue;
                }
                $raw = $quantities[$productId] ?? $quantities[(string) $productId] ?? null;
                $requested = filter_var($raw, FILTER_VALIDATE_INT);
                $current = (int) ($cart[$productId] ?? 0);
                if ($requested === false || $requested < 1) {
                    if ($current > 0) {
                        unset($cart[$productId]);
                        $changed = true;
                    }
                    continue;
                }
                $stock = self::transactional(static function (PDO $db) use ($productId): int {
                    $product = self::lockProductStock($db, $productId);
                    return (int) ($product['cantidad_disponible'] ?? 0);
                });
                $next = min((int) $requested, $stock);
                if ($next < 1) {
                    unset($cart[$productId]);
                    $changed = $changed || $current > 0;
                } elseif ($next !== $current) {
                    $cart[$productId] = $next;
                    $changed = true;
                }
            }
            $_SESSION['cart'] = $cart;
            self::invalidate();
            return $changed;
        }

        self::mergeSessionCart();
        $changed = self::transactional(static function (PDO $db) use ($userId, $quantities): bool {
            $cartId = self::lockActiveCart($db, $userId, false);
            if ($cartId === null) {
                return false;
            }

            $currentItems = self::lockCartItems($db, $cartId);
            $ids = array_map('intval', array_keys($quantities));
            sort($ids, SORT_NUMERIC);
            $changed = false;
            foreach ($ids as $productId) {
                if (!array_key_exists($productId, $currentItems)) {
                    continue;
                }
                $raw = $quantities[$productId] ?? $quantities[(string) $productId] ?? null;
                $requested = filter_var($raw, FILTER_VALIDATE_INT);
                $current = (int) $currentItems[$productId];
                if ($requested === false || $requested < 1) {
                    self::deleteLine($db, $cartId, $productId);
                    $changed = true;
                    continue;
                }

                $product = self::lockProductStock($db, $productId);
                $next = min((int) $requested, (int) ($product['cantidad_disponible'] ?? 0));
                if ($next < 1) {
                    self::deleteLine($db, $cartId, $productId);
                    $changed = true;
                } elseif ($next !== $current) {
                    $stmt = $db->prepare('UPDATE detalle_carrito SET cantidad=? WHERE id_carrito=? AND id_producto=?');
                    $stmt->execute([$next, $cartId, $productId]);
                    $changed = true;
                }
            }
            if ($changed) {
                self::touch($db, $cartId);
            }
            return $changed;
        });

        self::invalidate();
        return $changed;
    }

    public static function remove(int $productId): void
    {
        if ($productId < 1) {
            return;
        }
        $userId = Auth::id();
        if ($userId === null) {
            unset($_SESSION['cart'][$productId]);
            self::invalidate();
            return;
        }

        self::mergeSessionCart();
        self::transactional(static function (PDO $db) use ($userId, $productId): void {
            $cartId = self::lockActiveCart($db, $userId, false);
            if ($cartId !== null) {
                self::deleteLine($db, $cartId, $productId);
                self::touch($db, $cartId);
            }
        });
        self::invalidate();
    }

    /** Merge a guest basket after login without exceeding current stock. */
    public static function mergeSessionCart(): void
    {
        $userId = Auth::id();
        $guestItems = self::sessionItems();
        if ($userId === null || $guestItems === []) {
            return;
        }

        self::transactional(static function (PDO $db) use ($userId, $guestItems): void {
            $cartId = self::lockActiveCart($db, $userId, true);
            ksort($guestItems, SORT_NUMERIC);
            foreach ($guestItems as $productId => $quantity) {
                $product = self::lockProductStock($db, (int) $productId);
                $stock = (int) ($product['cantidad_disponible'] ?? 0);
                $current = self::cartQuantity($db, (int) $cartId, (int) $productId);
                $next = min($stock, $current + (int) $quantity);
                if ($next < 1) {
                    if ($current > 0) {
                        self::deleteLine($db, (int) $cartId, (int) $productId);
                    }
                    continue;
                }
                if ($current > 0) {
                    $stmt = $db->prepare('UPDATE detalle_carrito SET cantidad=? WHERE id_carrito=? AND id_producto=?');
                    $stmt->execute([$next, $cartId, $productId]);
                } else {
                    $stmt = $db->prepare('INSERT INTO detalle_carrito(id_carrito,id_producto,cantidad) VALUES(?,?,?)');
                    $stmt->execute([$cartId, $productId, $next]);
                }
            }
            self::touch($db, (int) $cartId);
        });

        unset($_SESSION['cart']);
        self::invalidate();
    }

    /** Called inside the checkout transaction; locks this user's active cart. */
    public static function lockActive(PDO $db, int $userId): ?int
    {
        return self::lockActiveCart($db, $userId, false);
    }

    /** Called inside the checkout transaction; returns product ID => quantity. */
    public static function lockItems(PDO $db, int $cartId): array
    {
        return self::lockCartItems($db, $cartId);
    }

    /** Keep successful purchases and their carts linked in the same commit. */
    public static function markConverted(PDO $db, int $cartId): void
    {
        $stmt = $db->prepare("UPDATE carritos SET estado='convertido' WHERE id_carrito=? AND estado='activo'");
        $stmt->execute([$cartId]);
        if ($stmt->rowCount() !== 1) {
            throw new RuntimeException('El carrito cambió durante la compra. Volvé a intentarlo.');
        }
    }

    public static function invalidate(): void
    {
        self::$itemsCache = null;
    }

    private static function transactional(callable $operation): mixed
    {
        $db = Database::connection();
        $db->beginTransaction();
        try {
            $result = $operation($db);
            $db->commit();
            return $result;
        } catch (Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $error;
        }
    }

    private static function lockActiveCart(PDO $db, int $userId, bool $create): ?int
    {
        $user = $db->prepare('SELECT id_usuario FROM usuarios WHERE id_usuario=? AND estado=1 FOR UPDATE');
        $user->execute([$userId]);
        if (!$user->fetchColumn()) {
            throw new RuntimeException('La cuenta no está disponible para usar el carrito.');
        }

        $query = $db->prepare("SELECT id_carrito FROM carritos WHERE id_usuario=? AND estado='activo' ORDER BY id_carrito DESC FOR UPDATE");
        $query->execute([$userId]);
        $activeIds = array_map('intval', $query->fetchAll(PDO::FETCH_COLUMN));
        if ($activeIds !== []) {
            $cartId = $activeIds[0];
            if (count($activeIds) > 1) {
                $abandon = $db->prepare("UPDATE carritos SET estado='abandonado' WHERE id_usuario=? AND estado='activo' AND id_carrito<>?");
                $abandon->execute([$userId, $cartId]);
            }
            return $cartId;
        }
        if (!$create) {
            return null;
        }

        $insert = $db->prepare(Database::insertReturning("INSERT INTO carritos(id_usuario,estado) VALUES(?,'activo')", 'id_carrito'));
        $insert->execute([$userId]);
        return Database::insertedId($db, $insert);
    }

    private static function lockCartItems(PDO $db, int $cartId): array
    {
        $query = $db->prepare('SELECT id_producto,cantidad FROM detalle_carrito WHERE id_carrito=? ORDER BY id_producto FOR UPDATE');
        $query->execute([$cartId]);
        $items = [];
        foreach ($query->fetchAll() as $row) {
            $items[(int) $row['id_producto']] = (int) $row['cantidad'];
        }
        return $items;
    }

    private static function lockProductStock(PDO $db, int $productId): ?array
    {
        $query = $db->prepare('SELECT p.id_producto,p.nombre,p.modelo,p.condicion,p.precio,p.publicado,p.estado,i.cantidad_disponible FROM productos p JOIN inventario i USING(id_producto) WHERE p.id_producto=? AND p.publicado=1 AND p.estado=1 FOR UPDATE');
        $query->execute([$productId]);
        $product = $query->fetch();
        return $product ?: null;
    }

    private static function cartQuantity(PDO $db, int $cartId, int $productId): int
    {
        $query = $db->prepare('SELECT cantidad FROM detalle_carrito WHERE id_carrito=? AND id_producto=?');
        $query->execute([$cartId, $productId]);
        return (int) ($query->fetchColumn() ?: 0);
    }

    private static function deleteLine(PDO $db, int $cartId, int $productId): void
    {
        $stmt = $db->prepare('DELETE FROM detalle_carrito WHERE id_carrito=? AND id_producto=?');
        $stmt->execute([$cartId, $productId]);
    }

    private static function touch(PDO $db, int $cartId): void
    {
        $stmt = $db->prepare('UPDATE carritos SET fecha_actualizacion=NOW() WHERE id_carrito=?');
        $stmt->execute([$cartId]);
    }

    private static function assertStock(int $stock, int $current, int $requested): void
    {
        if ($stock < 1) {
            throw new RuntimeException('Este producto está agotado y no se puede agregar.');
        }
        if ($current + $requested > $stock) {
            throw new RuntimeException('Solo hay ' . max(0, $stock - $current) . ' unidad(es) disponible(s) para agregar.');
        }
    }

    private static function sessionItems(): array
    {
        $raw = is_array($_SESSION['cart'] ?? null) ? $_SESSION['cart'] : [];
        $items = [];
        foreach ($raw as $productId => $quantity) {
            $id = filter_var($productId, FILTER_VALIDATE_INT);
            $count = filter_var($quantity, FILTER_VALIDATE_INT);
            if ($id !== false && $id > 0 && $count !== false && $count > 0) {
                $items[(int) $id] = (int) $count;
            }
        }
        ksort($items, SORT_NUMERIC);
        return $items;
    }
}
