<?php

class Accounts extends Model {
    public static function create () {
        return Database::query('CREATE TABLE `accounts` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(191) UNIQUE NOT NULL,
            `user_id` INT UNSIGNED NOT NULL,
            `amount` BIGINT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL
        )');
    }

    public static function fill () {
        static::insert([
            'name' => 'Bastiaan\'s Account',
            'user_id' => 1,
            'amount' => 100,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
