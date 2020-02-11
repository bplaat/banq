<?php

class Sessions extends Model {
    protected static $primaryKey = 'session';

    public static function create () {
        Database::query('CREATE TABLE `sessions` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `session` CHAR(32) UNIQUE NOT NULL,
            `user_id` INT UNSIGNED NOT NULL,
            `expires_at` DATETIME NOT NULL
        )');
    }
}
