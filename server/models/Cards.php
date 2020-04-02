<?php

class Cards extends Model {
    // Set the table dependencies of this model
    protected static $dependencies = [ 'Accounts' ];

    // The table fields validation rules
    const NAME_VALIDATION = 'required|min:3|max:35';
    const ACCOUNT_ID_VALIDATION = 'required|int|exists:Accounts,id';
    const ACCOUNT_ID_ADMIN_VALIDATION = 'required|int|exists:Accounts,id';
    const RFID_VALIDATION = 'required|min:8|max:255';
    const PINCODE_VALIDATION = 'required|min:4|max:255';

    // The cards create table function
    public static function create () {
        Database::query('CREATE TABLE `cards` (
            `id` INT UNSIGNED AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `account_id` INT UNSIGNED NOT NULL,
            `rfid` VARCHAR(255) NOT NULL,
            `pincode` VARCHAR(255) NOT NULL,
            `attempts` INT UNSIGNED NOT NULL DEFAULT 0,
            `blocked` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            FOREIGN KEY (`account_id`) REFERENCES `accounts`(`id`)
        )');
    }

    // A custom query function count cards by user
    public static function countByUser ($user_id) {
        return Database::query('SELECT COUNT(*) FROM `cards` WHERE `account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?)', $user_id)->fetch()->{'COUNT(*)'};
    }

    // A custom query function paged select cards by user
    public static function selectPageByUser ($user_id, $page, $per_page) {
        return Database::query('SELECT * FROM `cards` WHERE `account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) ORDER BY `created_at` DESC LIMIT ?, ?', $user_id, ($page - 1) * $per_page, $per_page);
    }

    // A custom query function count cards by search query
    public static function searchCount ($q) {
        $q = '%' . $q . '%';
        return Database::query('SELECT COUNT(*) FROM `cards` WHERE `name` LIKE ?', $q)->fetch()->{'COUNT(*)'};
    }

    // A custom query function paged select cards by search query
    public static function searchSelectPage ($q, $page, $per_page) {
        $q = '%' . $q . '%';
        return Database::query('SELECT * FROM `cards` WHERE `name` LIKE ? ORDER BY `created_at` DESC LIMIT ?, ?', $q, ($page - 1) * $per_page, $per_page);
    }

    // A custom query function count cards by user by search query
    public static function searchCountByUser($user_id, $q) {
        $q = '%' . $q . '%';
        return Database::query('SELECT COUNT(*) FROM `cards` WHERE `account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) AND `name` LIKE ?', $user_id, $q)->fetch()->{'COUNT(*)'};
    }

    // A custom query function paged select cards by user by search query
    public static function searchSelectPageByUser ($user_id, $q, $page, $per_page) {
        $q = '%' . $q . '%';
        return Database::query('SELECT * FROM `cards` WHERE `account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) AND `name` LIKE ? ORDER BY `created_at` DESC LIMIT ?, ?', $user_id, $q, ($page - 1) * $per_page, $per_page);
    }
}
