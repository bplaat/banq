<?php

class Transactions extends Model {
    public static function create () {
        return Database::query('CREATE TABLE `transactions` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(191) NOT NULL,
            `from_account_id` INT UNSIGNED NOT NULL,
            `to_account_id` INT UNSIGNED NOT NULL,
            `amount` BIGINT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL
        )');
    }

    public static function selectAll ($max) {
        return Database::query('SELECT * FROM `transactions` WHERE `from_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) OR `to_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) ORDER BY `created_at` DESC LIMIT ?', Auth::id(), Auth::id(), $max);
    }

    public static function selectAllByAccount ($account_id) {
        return Database::query('SELECT * FROM `transactions` WHERE `from_account_id` = ? OR `to_account_id` = ? ORDER BY `created_at` DESC', $account_id, $account_id);
    }
}
