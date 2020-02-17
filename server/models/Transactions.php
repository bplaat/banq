<?php

class Transactions extends Model {
    const NAME_VALIDATION = 'required|min:3|max:35';
    const FROM_ACCOUNT_ID_VALIDATION = 'required|int|different:to_account_id|exists:Accounts,id';
    const TO_ACCOUNT_ID_VALIDATION = 'required|int|different:from_account_id|exists:Accounts,id';
    const AMOUNT_VALIDATION = 'required|int|number_min:1';

    public static function create () {
        return Database::query('CREATE TABLE `transactions` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `from_account_id` INT UNSIGNED NOT NULL,
            `to_account_id` INT UNSIGNED NOT NULL,
            `amount` BIGINT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )');
    }

    public static function countAllByUser () {
        return Database::query('SELECT COUNT(`id`) as `count` FROM `transactions` WHERE `from_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) OR `to_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?)', Auth::id(), Auth::id())->fetch()->count;
    }

    public static function selectAllByUser ($page, $per_page) {
        return Database::query('SELECT * FROM `transactions` WHERE `from_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) OR `to_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) ORDER BY `created_at` DESC LIMIT ?, ?', Auth::id(), Auth::id(), ($page - 1) * $per_page, $per_page);
    }

    public static function countAllByAccount ($account_id) {
        return Database::query('SELECT COUNT(`id`) as `count` FROM `transactions` WHERE `from_account_id` = ? OR `to_account_id` = ?', $account_id, $account_id)->fetch()->count;
    }

    public static function selectAllByAccount ($account_id, $page, $per_page) {
        return Database::query('SELECT * FROM `transactions` WHERE `from_account_id` = ? OR `to_account_id` = ? ORDER BY `created_at` DESC LIMIT ?, ?', $account_id, $account_id, ($page - 1) * $per_page, $per_page);
    }
}
