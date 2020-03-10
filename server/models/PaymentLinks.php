<?php

class PaymentLinks extends Model {
    // Set the table name of this model
    protected static $table = 'payment_links';

    // The table fields validation rules
    const NAME_VALIDATION = 'required|min:3|max:35';
    const ACCOUNT_ID_VALIDATION = 'required|int|exists:Accounts,id|@Accounts::RIGHT_OWNER_VALIDATION|@Accounts::ONLY_PAYMENT_VALIDATION';
    const ACCOUNT_ID_ADMIN_VALIDATION = 'required|int|exists:Accounts,id|@Accounts::ONLY_PAYMENT_VALIDATION';
    const AMOUNT_VALIDATION = 'required|float|number_min:0.01';

    // The payment links create table function
    public static function create () {
        return Database::query('CREATE TABLE `payment_links` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `link` VARCHAR(32) UNIQUE NOT NULL,
            `account_id` INT UNSIGNED NOT NULL,
            `amount` DECIMAL(15,2) UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )');
    }

    // A function that generates a short unqiue id for a payment link
    public static function generateLink () {
        $link = '';
        for ($i = 0; $i < 10; $i++) {
            $link .= substr(str_shuffle("0123456789bcdfghjklmnpqrstvwxyz"), 0, 1);
        }
        if (static::select($link)->rowCount() == 1) {
            return static::generateLink();
        }
        return $link;
    }

    // A custom query function count payment links by user
    public static function countByUser ($user_id) {
        return Database::query('SELECT COUNT(*) FROM `payment_links` WHERE `account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?)', $user_id)->fetch()->{'COUNT(*)'};
    }

    // A custom query function paged select payment links by user
    public static function selectPageByUser ($user_id, $page, $per_page) {
        return Database::query('SELECT * FROM `payment_links` WHERE `account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) ORDER BY `created_at` DESC LIMIT ?, ?', $user_id, ($page - 1) * $per_page, $per_page);
    }

    // A custom query function count payment links by search query
    public static function searchCount ($q) {
        $q = '%' . $q . '%';
        return Database::query('SELECT COUNT(*) FROM `payment_links` WHERE `name` LIKE ?', $q)->fetch()->{'COUNT(*)'};
    }

    // A custom query function paged select payment links by search query
    public static function searchSelectPage ($q, $page, $per_page) {
        $q = '%' . $q . '%';
        return Database::query('SELECT * FROM `payment_links` WHERE `name` LIKE ? ORDER BY `created_at` DESC LIMIT ?, ?', $q, ($page - 1) * $per_page, $per_page);
    }

    // A custom query function count payment links by user by search query
    public static function searchCountByUser($user_id, $q) {
        $q = '%' . $q . '%';
        return Database::query('SELECT COUNT(*) FROM `payment_links` WHERE `account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) AND `name` LIKE ?', $user_id, $q)->fetch()->{'COUNT(*)'};
    }

    // A custom query function paged select payment links by user by search query
    public static function searchSelectPageByUser ($user_id, $q, $page, $per_page) {
        $q = '%' . $q . '%';
        return Database::query('SELECT * FROM `payment_links` WHERE `account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) AND `name` LIKE ? ORDER BY `created_at` DESC LIMIT ?, ?', $user_id, $q, ($page - 1) * $per_page, $per_page);
    }
}
