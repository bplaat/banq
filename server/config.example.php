<?php

// The Banq config file

// The app constants
define('APP_NAME', 'Banq');
define('APP_VERSION', '0.1');
define('APP_DEBUG', isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'banq.local');

// The database constants
define('DATABASE_DSN', 'mysql:host=127.0.0.1;dbname=banq');
define('DATABASE_USER', 'banq');
define('DATABASE_PASSWORD', 'banq');

// The session constants
define('SESSION_COOKIE_NAME', 'banq-session');
define('SESSION_DURATION', 60 * 60 * 24 * 356);
define('SESSION_UPDATE_DURATION', 60);
define('SESSION_SHORT_COOKIE_NAME', 'banq-short-session');

// The interest rate constant in procent
define('INTEREST_RATE', 1);

// The check if the client is the mobile Android app
define('IS_MOBILE_APP', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'ml.banq.android');
