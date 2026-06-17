<?php

declare(strict_types=1);

define('ROOT_PATH', __DIR__);
define('SRC_PATH',  ROOT_PATH . '/src');
define('VIEW_PATH', ROOT_PATH . '/views');
define('UPLOAD_DIR', ROOT_PATH . '/uploads');

$appConfig = require ROOT_PATH . '/config/app.php';

define('BASE_URL',   $appConfig['baseUrl']);
define('UPLOAD_URL', $appConfig['uploadUrl']);
define('APP_NAME',   $appConfig['name']);

date_default_timezone_set($appConfig['timezone']);

session_start();

spl_autoload_register(function (string $className): void {
    $relativePath = str_replace(['App\\', '\\'], ['', '/'], $className);
    $absolutePath = SRC_PATH . '/' . $relativePath . '.php';

    if (file_exists($absolutePath)) {
        require_once $absolutePath;
    }
});

require_once SRC_PATH . '/Helpers/functions.php';

use App\Http\Request;
use App\Router;

$request = new Request();
$router  = new Router($request);

require ROOT_PATH . '/config/routes.php';

$router->dispatch();
