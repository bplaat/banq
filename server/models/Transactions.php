<?php

class Transactions extends Model {
    // The fields validation rules
    const NAME_VALIDATION = 'required|min:3|max:35';
    const FROM_ACCOUNT_ID_VALIDATION = 'required|int|different:to_account_id|exists:Accounts,id|@Accounts::RIGHT_OWNER_VALIDATION|@Accounts::ENOUGH_AMOUNT_VALIDATION';
    const FROM_ACCOUNT_ID_ADMIN_VALIDATION = 'required|int|different:to_account_id|exists:Accounts,id|@Accounts::ENOUGH_AMOUNT_VALIDATION';
    const TO_ACCOUNT_ID_VALIDATION = 'required|int|different:from_account_id|exists:Accounts,id';
    const AMOUNT_VALIDATION = 'required|float|number_min:0.01';

    // The transactions create table function
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

    // A custom query function which counts all the transaction of a user
    public static function countAllByUser () {
        return Database::query('SELECT COUNT(`id`) as `count` FROM `transactions` WHERE `from_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) OR `to_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?)', Auth::id(), Auth::id())->fetch()->count;
    }

    // A custom query function which select all the transaction of a user by page
    public static function selectAllByUser ($page, $per_page) {
        return Database::query('SELECT * FROM `transactions` WHERE `from_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) OR `to_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) ORDER BY `created_at` DESC LIMIT ?, ?', Auth::id(), Auth::id(), ($page - 1) * $per_page, $per_page);
    }

    // A custom query function which counts all the transactions of a account
    public static function countAllByAccount ($account_id) {
        return Database::query('SELECT COUNT(`id`) as `count` FROM `transactions` WHERE `from_account_id` = ? OR `to_account_id` = ?', $account_id, $account_id)->fetch()->count;
    }

    // A custom query function which select all the transactions of account by page
    public static function selectAllByAccount ($account_id, $page, $per_page) {
        return Database::query('SELECT * FROM `transactions` WHERE `from_account_id` = ? OR `to_account_id` = ? ORDER BY `created_at` DESC LIMIT ?, ?', $account_id, $account_id, ($page - 1) * $per_page, $per_page);
    }

    // A custom query search count function
    public static function searchCount ($q) {
        $q = '%' . $q . '%';
        return Database::query('SELECT COUNT(`id`) as `count` FROM `transactions` WHERE `name` LIKE ?', $q)->fetch()->count;
    }

    // A custom query search select function
    public static function searchPage ($q, $page, $per_page) {
        $q = '%' . $q . '%';
        return Database::query('SELECT * FROM `transactions` WHERE `name` LIKE ? LIMIT ?, ?', $q, ($page - 1) * $per_page, $per_page);
    }
}
