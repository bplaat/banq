<?php

class Sessions extends Model {
    protected static $primaryKey = 'session';

    public static function create () {
        Database::query('CREATE TABLE `sessions` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `session` CHAR(32) UNIQUE NOT NULL,
            `user_id` INT UNSIGNED NOT NULL,
            `ip` VARCHAR(32) NOT NULL,
            `browser` VARCHAR(32) NOT NULL,
            `version` VARCHAR(32) NOT NULL,
            `platform` VARCHAR(32) NOT NULL,
            `expires_at` DATETIME NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )');
    }
}
