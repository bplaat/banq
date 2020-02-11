<?php

define('DEBUG', true);

define('DATABASE_DSN', 'mysql:host=127.0.0.1;dbname=banq');
define('DATABASE_USER', 'banq');
define('DATABASE_PASSWORD', ''); // Your database password

define('SESSION_COOKIE_NAME', 'banq-session');
define('SESSION_DURATION', 60 * 60 * 24 * 356);

define('USER_ROLE_NORMAL', 1);
define('USER_ROLE_ADMIN', 2);
