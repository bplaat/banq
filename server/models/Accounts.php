<?php

class Accounts extends Model {
    const TYPE_SAVE = 1;
    const TYPE_PAYMENT = 2;

    const MAX_COUNT = 6;
    const NAME_VALIDATION = 'required|min:3|max:35';
    const TYPE_VALIDATION = 'required|int|number_between:1,2';
    const USER_ID_VALIDATION = 'required|int|exits:User,id';
    const AMOUNT_VALIDATION = 'required|float|number_min:0';

    public static function MAX_COUNT_VALIDATION ($key, $value) {
        if (static::select([ $key => $value ])->rowCount() >= static::MAX_COUNT) {
            return 'You can create a maximum of ' . static::MAX_COUNT . ' accounts';
        }
    }

    public static function RIGHT_OWNER_VALIDATION ($key, $value) {
        $account = Accounts::select($value)->fetch();
        if ($account->user_id != Auth::id()) {
            return 'The account \'' . $account->name . '\' is not yours';
        }
    }

    public static function ENOUGH_AMOUNT_VALIDATION ($key, $value) {
        $account = Accounts::select($value)->fetch();
        $amount = parse_money_number(request('amount'));
        if ($account->amount - $amount < 0) {
            return 'The account \'' . $account->name . '\' does not have enough money for this transaction';
        }
    }

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
}
