<?php

if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'banq.local') {
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
define('SESSION_SHORT_COOKIE_NAME', 'banq-short-session');

define('INTEREST_RATE', 1);

define('IS_MOBILE_APP', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'ml.banq.android');
