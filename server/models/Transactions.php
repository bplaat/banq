<?php

class Transactions extends Model {
    public static function create () {
        return Database::query('CREATE TABLE `transactions` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `from_user_id` INT UNSIGNED NOT NULL,
            `to_user_id` INT UNSIGNED NOT NULL,
            `amount` BIGINT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL
        )');
    }
}
