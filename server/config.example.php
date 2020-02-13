<?php

if ($_SERVER['HTTP_HOST'] == 'banq.local') {
    define('DEBUG', true);
} else {
    define('DEBUG', false);
    $_SERVER['HTTP_HOST'] = 'banq.ml';
    $_SERVER['HTTPS'] = 'on';
}

define('DATABASE_DSN', 'mysql:host=127.0.0.1;dbname=banq');
define('DATABASE_USER', 'banq');
define('DATABASE_PASSWORD', ''); // Your database password

define('SESSION_COOKIE_NAME', 'banq-session');
define('SESSION_DURATION', 60 * 60 * 24 * 356);

define('IS_MOBILE_APP', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'ml.banq.android');
