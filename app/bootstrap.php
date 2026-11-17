<?php
declare(strict_types=1);
define('ROOT', dirname(__DIR__));
require ROOT . '/config/config.php';
require ROOT . '/app/Core/Database.php';
require ROOT . '/app/Core/Router.php';
require ROOT . '/app/Services/Auth.php';
require ROOT . '/app/Services/Cart.php';
require ROOT . '/app/Services/Store.php';
require ROOT . '/app/Services/ProductArtwork.php';
require ROOT . '/app/Controllers/StoreController.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_save_path(ROOT . '/storage/sessions');
    session_name((string) config('SESSION_NAME', 'kroma_session'));
    ini_set('session.gc_maxlifetime', '1800');
    $isHttps = !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    session_set_cookie_params(['httponly'=>true, 'samesite'=>'Lax', 'secure'=>$isHttps]);
    session_start();
}
if (!isset($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(32));
date_default_timezone_set('America/Argentina/Buenos_Aires');
set_exception_handler(function (Throwable $e): void { error_log((string)$e); file_put_contents(ROOT.'/storage/app.log', '['.date('c').'] '.(string)$e.PHP_EOL, FILE_APPEND | LOCK_EX); http_response_code(500); if (config('APP_DEBUG', false)) echo '<pre>Error interno: '.htmlspecialchars($e->getMessage()).'</pre>'; else echo 'Ocurrió un error. Intentá nuevamente.'; });
Auth::validateActiveSession();
(new Router())->dispatch();
