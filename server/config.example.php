<?php

define('APP_NAME', 'Banq');
define('APP_VERSION', '0.1');
define('APP_DEBUG', isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'banq.local');

define('DATABASE_DSN', 'mysql:host=127.0.0.1;dbname=banq');
define('DATABASE_USER', 'banq');
define('DATABASE_PASSWORD', 'banq');

define('SESSION_COOKIE_NAME', 'banq-session');
define('SESSION_DURATION', 60 * 60 * 24 * 356);
define('SESSION_UPDATE_DURATION', 60);
define('SESSION_SHORT_COOKIE_NAME', 'banq-short-session');

define('INTEREST_RATE', 1);

define('IS_MOBILE_APP', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'ml.banq.android');
