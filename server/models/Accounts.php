<?php

class Accounts extends Model {
    const MAX_COUNT = 5;
    const NAME_MIN_LENGTH = 3;
    const NAME_MAX_LENGTH = 25;

    public static function create () {
        return Database::query('CREATE TABLE `accounts` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(' . NAME_MAX_LENGTH . ') NOT NULL,
            `user_id` INT UNSIGNED NOT NULL,
            `amount` BIGINT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL
        )');
    }

    public static function fill () {
        // Bastiaan's accounts
        static::insert([
            'name' => 'Bastiaan\'s Account',
            'user_id' => 1,
            'amount' => 100,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        static::insert([
            'name' => 'Bastiaan\'s Spaarpotje',
            'user_id' => 1,
            'amount' => 50,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Jan's accounts
        static::insert([
            'name' => 'Jan\'s Account',
            'user_id' => 2,
            'amount' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        static::insert([
            'name' => 'Jan\'s Spaarpotje',
            'user_id' => 2,
            'amount' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
