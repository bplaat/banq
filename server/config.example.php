<?php

// The Banq config file

// The app constants
define('APP_NAME', 'Banq');
define('APP_VERSION', '0.3');
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

// The pagination limits
define('PAGINATION_LIMIT_NORMAL', 6);
define('PAGINATION_LIMIT_ADMIN', 12);
define('PAGINATION_LIMIT_API', 20);
define('PAGINATION_MAX_LIMIT_API', 50);

// Bank constants
define('COUNTRY_CODE', 'SU');
define('BANK_CODE', 'BANQ');

// The max accounts count per user
define('ACCOUNTS_MAX_COUNT', 6);

// Special admin account ids
define('ADMIN_WITHDRAW_ACCOUNT_ID', 1);
define('ADMIN_INTEREST_ACCOUNT_ID', 2);
define('ADMIN_DELETED_ACCOUNT_ID', 3);

// The interest rate constant in procent
define('INTEREST_RATE', 1);

// The card max attempts
define('CARD_MAX_ATTEMPTS', 3);

// The check if the client is the mobile Android app
define('IS_MOBILE_APP', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'ml.banq.android');

// Gosbanks status codes
define('GOSBANK_CODE_SUCCESS', 200);
define('GOSBANK_CODE_BROKEN_MESSAGE', 400);
define('GOSBANK_CODE_AUTH_FAILED', 401);
define('GOSBANK_CODE_NOT_ENOUGH_BALANCE', 402);
define('GOSBANK_CODE_BLOCKED', 403);
define('GOSBANK_CODE_DONT_EXISTS', 404);
