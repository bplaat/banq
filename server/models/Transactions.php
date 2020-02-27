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

    // A custom query function count transactions by user
    public static function countByUser ($user_id) {
        return Database::query('SELECT COUNT(`id`) as `count` FROM `transactions` WHERE `from_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) OR `to_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?)', $user_id, $user_id)->fetch()->count;
    }

    // A custom query function paged select transactions by user
    public static function selectPageByUser ($user_id, $page, $per_page) {
        return Database::query('SELECT * FROM `transactions` WHERE `from_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) OR `to_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) ORDER BY `created_at` DESC LIMIT ?, ?', $user_id, $user_id, ($page - 1) * $per_page, $per_page);
    }

    // A custom query function count transactions by account
    public static function countByAccount ($account_id) {
        return Database::query('SELECT COUNT(`id`) as `count` FROM `transactions` WHERE `from_account_id` = ? OR `to_account_id` = ?', $account_id, $account_id)->fetch()->count;
    }

    // A custom query function paged select transactions by acount
    public static function selectPageByAccount ($account_id, $page, $per_page) {
        return Database::query('SELECT * FROM `transactions` WHERE `from_account_id` = ? OR `to_account_id` = ? ORDER BY `created_at` DESC LIMIT ?, ?', $account_id, $account_id, ($page - 1) * $per_page, $per_page);
    }

    // A custom query function count transactions by search query
    public static function searchCount ($q) {
        $q = '%' . $q . '%';
        return Database::query('SELECT COUNT(`id`) as `count` FROM `transactions` WHERE `name` LIKE ?', $q)->fetch()->count;
    }

    // A custom query function paged select by search query
    public static function searchSelectPage ($q, $page, $per_page) {
        $q = '%' . $q . '%';
        return Database::query('SELECT * FROM `transactions` WHERE `name` LIKE ? LIMIT ?, ?', $q, ($page - 1) * $per_page, $per_page);
    }

    // A custom query function count transactions by user by search query
    public static function searchCountByUser ($user_id, $q) {
        $q = '%' . $q . '%';
        return Database::query('SELECT COUNT(`id`) as `count` FROM `transactions` WHERE `from_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) OR `to_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) AND `name` LIKE ?', $user_id, $q)->fetch()->count;
    }

    // A custom query function paged select by by user search query
    public static function searchSelectPageByUser ($user_id, $q, $page, $per_page) {
        $q = '%' . $q . '%';
        return Database::query('SELECT * FROM `transactions` WHERE `from_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) OR `to_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) AND `name` LIKE ? LIMIT ?, ?', $user_id, $q, ($page - 1) * $per_page, $per_page);
    }
}
