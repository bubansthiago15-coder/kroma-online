<?php
declare(strict_types=1);

final class Auth
{
    private const IDLE_MINUTES = 30;

    private static function idleCutoffSql(): string
    {
        return Database::driver() === 'pgsql'
            ? "CURRENT_TIMESTAMP - INTERVAL '" . self::IDLE_MINUTES . " minutes'"
            : 'DATE_SUB(NOW(), INTERVAL ' . self::IDLE_MINUTES . ' MINUTE)';
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user']['id_usuario']) ? (int) $_SESSION['user']['id_usuario'] : null;
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function requireLogin(): void
    {
        if (!self::id()) {
            $_SESSION['flash'] = 'Iniciá sesión para continuar.';
            header('Location: ?action=login');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (($_SESSION['user']['rol'] ?? '') !== 'administrador') {
            http_response_code(403);
            exit('Acceso restringido.');
        }
    }

    public static function requireCustomer(): void
    {
        self::requireLogin();
        if (($_SESSION['user']['rol'] ?? '') !== 'cliente') {
            http_response_code(403);
            exit('Esta función está disponible solo para cuentas de cliente.');
        }
    }

    public static function login(array $user): bool
    {
        session_regenerate_id(true);
        $token = hash('sha256', session_id());
        $db = Database::connection();
        $db->beginTransaction();
        try {
            $cutoff = self::idleCutoffSql();
            $query = $db->prepare('SELECT token_sesion,CASE WHEN sesion_actividad >= ' . $cutoff . ' THEN 1 ELSE 0 END activa FROM usuarios WHERE id_usuario=? FOR UPDATE');
            $query->execute([(int) $user['id_usuario']]);
            $active = $query->fetch();
            if (!$active) {
                $db->rollBack();
                return false;
            }
            if (!empty($active['token_sesion']) && (int) $active['activa'] === 1 && !hash_equals((string) $active['token_sesion'], $token)) {
                $db->rollBack();
                return false;
            }
            $update = $db->prepare('UPDATE usuarios SET token_sesion=?,sesion_actividad=NOW(),fecha_ultimo_acceso=NOW() WHERE id_usuario=?');
            $update->execute([$token, (int) $user['id_usuario']]);
            $db->commit();
            $_SESSION['user'] = [
                'id_usuario' => (int) $user['id_usuario'],
                'nombre' => $user['nombre'],
                'apellido' => $user['apellido'],
                'email' => $user['email'],
                'rol' => $user['rol'],
            ];
            return true;
        } catch (Throwable $error) {
            if ($db->inTransaction()) $db->rollBack();
            throw $error;
        }
    }

    public static function validateActiveSession(): void
    {
        $userId = self::id();
        if (!$userId) return;
        $token = hash('sha256', session_id());
        $db = Database::connection();
        $cutoff = self::idleCutoffSql();
        $query = $db->prepare('SELECT u.token_sesion,r.nombre rol FROM usuarios u JOIN roles r USING(id_rol) WHERE u.id_usuario=? AND u.token_sesion=? AND u.sesion_actividad >= ' . $cutoff);
        $query->execute([$userId, $token]);
        $active = $query->fetch();
        if ($active) {
            $_SESSION['user']['rol'] = $active['rol'];
            $touch = $db->prepare('UPDATE usuarios SET sesion_actividad=NOW() WHERE id_usuario=? AND token_sesion=?');
            $touch->execute([$userId, $token]);
            return;
        }
        $claim = $db->prepare('UPDATE usuarios SET token_sesion=?,sesion_actividad=NOW() WHERE id_usuario=? AND (token_sesion IS NULL OR sesion_actividad < ' . $cutoff . ')');
        $claim->execute([$token, $userId]);
        if ($claim->rowCount() === 1) {
            $role = $db->prepare('SELECT r.nombre FROM usuarios u JOIN roles r USING(id_rol) WHERE u.id_usuario=?');
            $role->execute([$userId]);
            $_SESSION['user']['rol'] = $role->fetchColumn() ?: 'cliente';
            return;
        }
        $expire = $db->prepare('UPDATE usuarios SET token_sesion=NULL,sesion_actividad=NULL WHERE id_usuario=? AND token_sesion=? AND (sesion_actividad IS NULL OR sesion_actividad < ' . $cutoff . ')');
        $expire->execute([$userId, $token]);
        unset($_SESSION['user']);
        $_SESSION['flash'] = 'Tu sesión venció o se cerró. Iniciá sesión nuevamente.';
    }

    public static function logout(): void
    {
        $userId = self::id();
        if ($userId) {
            $clear = Database::connection()->prepare('UPDATE usuarios SET token_sesion=NULL,sesion_actividad=NULL WHERE id_usuario=? AND token_sesion=?');
            $clear->execute([$userId, hash('sha256', session_id())]);
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
}
