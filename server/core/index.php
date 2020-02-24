<?php

spl_autoload_register(function ($class) {
    $file = ROOT . '/core/' . $class . '.php';
    if (file_exists($file)) require_once $file;
});

spl_autoload_register(function ($class) {
    $file = ROOT . '/controllers/' . $class . '.php';
    if (file_exists($file)) require_once $file;
});

spl_autoload_register(function ($class) {
    $file = ROOT . '/models/' . $class . '.php';
    if (file_exists($file)) require_once $file;
});

require_once ROOT . '/config.php';

Session::init();

require_once ROOT . '/core/parse_user_agent.php';
require_once ROOT . '/core/validate.php';
require_once ROOT . '/core/view.php';
require_once ROOT . '/core/utils.php';

if (APP_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

Database::connect(DATABASE_DSN, DATABASE_USER, DATABASE_PASSWORD);

if (APP_DEBUG) {
    require_once ROOT . '/core/debug_routes.php';
}

require_once ROOT . '/routes.php';
