<?php

class PaymentLinks extends Model {
    protected static $table = 'payment_links';
    protected static $primaryKey = 'link';

    const NAME_VALIDATION = 'required|min:3|max:35';
    const ACCOUNT_ID_VALIDATION = 'required|int|exists:Accounts,id';
    const AMOUNT_VALIDATION = 'required|float|number_min:0.01';

    public static function create () {
        return Database::query('CREATE TABLE `payment_links` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `link` VARCHAR(32) UNIQUE NOT NULL,
            `account_id` INT UNSIGNED NOT NULL,
            `amount` BIGINT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )');
    }

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

    public static function countAllByUser () {
        return Database::query('SELECT COUNT(`id`) as `count` FROM `payment_links` WHERE `account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?)', Auth::id())->fetch()->count;
    }

    public static function selectAllByUser ($page, $per_page) {
        return Database::query('SELECT * FROM `payment_links` WHERE `account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) ORDER BY `created_at` DESC LIMIT ?, ?', Auth::id(), ($page - 1) * $per_page, $per_page);
    }
}
