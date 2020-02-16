<?php

class Transactions extends Model {
    const NAME_VALIDATION = 'required|min:3|max:35';
    const FROM_ACCOUNT_ID_VALIDATION = 'required|int|different:to_account_id|exists:Accounts,id';
    const TO_ACCOUNT_ID_VALIDATION = 'required|int|different:from_account_id|exists:Accounts,id';
    const AMOUNT_VALIDATION = 'required|int|number_min:1';

    public static function RIGHT_OWNER_VALIDATION ($key, $value) {
        $account = Accounts::select($value)->fetch();
        if ($account->user_id != Auth::id()) {
            return 'The account \'' . $account->name . '\' is not yours';
        }
    }

    public static function ENOUGH_AMOUNT_VALIDATION ($key, $value) {
        $account = Accounts::select($value)->fetch();
        if ($account->amount - request('amount') < 0) {
            return 'The account \'' . $account->name . '\' does not have enough money for this transaction';
        }
    }

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

    public static function selectAll ($max) {
        return Database::query('SELECT * FROM `transactions` WHERE `from_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) OR `to_account_id` IN (SELECT `id` FROM `accounts` WHERE `user_id` = ?) ORDER BY `created_at` DESC LIMIT ?', Auth::id(), Auth::id(), $max);
    }

    public static function selectAllByAccount ($account_id) {
        return Database::query('SELECT * FROM `transactions` WHERE `from_account_id` = ? OR `to_account_id` = ? ORDER BY `created_at` DESC', $account_id, $account_id);
    }
}
