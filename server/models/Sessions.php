<?php

class Sessions extends Model {
    // Make the primary key in the model the session field
    protected static $primaryKey = 'session';

    // The sessions create table function
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

    // A function that generates a new session key
    public static function generateSession () {
        $session = bin2hex(random_bytes(16));
        if (static::select($session)->rowCount() == 1) {
            return static::generateSession();
        }
        return $session;
    }

    // A custom query function which selects all the active session by user
    public static function selectAllActiveByUser ($user_id) {
        return Database::query('SELECT * FROM `sessions` WHERE `user_id` = ? AND `expires_at` > NOW() ORDER BY `updated_at` DESC', $user_id);
    }
}
