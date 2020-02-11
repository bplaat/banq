<?php

spl_autoload_register(function ($class) {
    $file = ROOT . '/core/' . $class . '.php';
    if (file_exists($file)) require_once $file;
});

require_once ROOT . '/core/utils.php';

spl_autoload_register(function ($class) {
    $file = ROOT . '/controllers/' . $class . '.php';
    if (file_exists($file)) require_once $file;
});

spl_autoload_register(function ($class) {
    $file = ROOT . '/models/' . $class . '.php';
    if (file_exists($file)) require_once $file;
});

require_once ROOT . '/config.php';

if (DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

Database::connect(DATABASE_DSN, DATABASE_USER, DATABASE_PASSWORD);

if (DEBUG) {
    Router::get('/debug/migrate', function () {
        $paths = glob(ROOT . '/models/*');
        foreach ($paths as $path) {
            $class = pathinfo($path, PATHINFO_FILENAME);
            call_user_func($class . '::drop');
            call_user_func($class . '::create');
            if (method_exists($class, 'fill')) {
                call_user_func($class . '::fill');
            }
        }
        Router::redirect('/');
    });
}

require_once ROOT . '/routes.php';
