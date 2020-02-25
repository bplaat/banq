<?php

class Accounts extends Model {
    // The table fields validation rules
    const NAME_VALIDATION = 'required|min:3|max:35';
    const TYPE_VALIDATION = 'required|int|number_between:1,2';
    const USER_ID_VALIDATION = 'required|int|exits:User,id|@Accounts::MAX_COUNT_VALIDATION';
    const AMOUNT_VALIDATION = 'required|float|number_min:0';

    // A custom validation rule which checks if the account is a payment account
    public static function ONLY_PAYMENT_VALIDATION ($key, $value) {
        $account = static::select($value)->fetch();
        if ($account->type != static::TYPE_PAYMENT) {
            return 'The account \'' . $account->name . '\' is not a payment account';
        }
    }

    // A custom validation rule which checks that you can max create sum amount of accounts
    const MAX_COUNT = 6;
    public static function MAX_COUNT_VALIDATION ($key, $value) {
        if (static::select([ $key => $value ])->rowCount() >= static::MAX_COUNT) {
            return 'You can create a maximum of ' . static::MAX_COUNT . ' accounts';
        }
    }

    // A custom validation rule which checks if the account is from the authed user
    public static function RIGHT_OWNER_VALIDATION ($key, $value) {
        $account = static::select($value)->fetch();
        if ($account->user_id != Auth::id()) {
            return 'The account \'' . $account->name . '\' is not yours';
        }
    }

    // A custom validation rule which checks if the account has enough money for the transaction
    public static function ENOUGH_AMOUNT_VALIDATION ($key, $value) {
        $account = static::select($value)->fetch();
        $amount = parse_money_number(request('amount'));
        if ($account->amount - $amount < 0) {
            return 'The account \'' . $account->name . '\' does not have enough money for this transaction';
        }
    }

    // The account types
    const TYPE_SAVE = 1;
    const TYPE_PAYMENT = 2;

    // The accounts create table function
    public static function create () {
        return Database::query('CREATE TABLE `accounts` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `type` INT UNSIGNED NOT NULL,
            `user_id` INT UNSIGNED NOT NULL,
            `amount` BIGINT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )');
    }

    // A custom query search count function
    public static function searchCount ($q) {
        $q = '%' . $q . '%';
        return Database::query('SELECT COUNT(`id`) as `count` FROM `accounts` WHERE `name` LIKE ?', $q)->fetch()->count;
    }

    // A custom query search select function
    public static function searchPage ($q, $page, $per_page) {
        $q = '%' . $q . '%';
        return Database::query('SELECT * FROM `accounts` WHERE `name` LIKE ? LIMIT ?, ?', $q, ($page - 1) * $per_page, $per_page);
    }
}
