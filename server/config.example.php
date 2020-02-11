<?php

define('DEBUG', true);

define('DATABASE_DSN', 'mysql:host=127.0.0.1;dbname=bank');
define('DATABASE_USER', 'bank');
define('DATABASE_PASSWORD', ''); // Your database password

define('SESSION_COOKIE_NAME', 'bank-session');
define('SESSION_DURATION', 60 * 60 * 24 * 356);

define('USER_ROLE_NORMAL', 1);
define('USER_ROLE_ADMIN', 2);
