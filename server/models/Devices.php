<?php

class Devices extends Model {
    // The table fields validation rules
    const NAME_VALIDATION = 'required|min:3|max:35';

    // The devices create table function
    public static function create () {
        Database::query('CREATE TABLE `devices` (
            `id` INT UNSIGNED AUTO_INCREMENT,
            `name` VARCHAR(35) NOT NULL,
            `key` CHAR(32) NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        )');
    }

    // A function that generates a new device key
    public static function generateKey () {
        $key = bin2hex(random_bytes(16));
        if (Devices::select([ 'key' => $key ])->rowCount() == 1) {
            return static::generateSession();
        }
        return $key;
    }

    // A custom query function count devices by search query
    public static function searchCount ($q) {
        $q = '%' . $q . '%';
        return Database::query('SELECT COUNT(*) FROM `devices` WHERE `name` LIKE ?', $q)->fetch()->{'COUNT(*)'};
    }

    // A custom query function paged select devices by search query
    public static function searchSelectPage ($q, $page, $per_page) {
        $q = '%' . $q . '%';
        return Database::query('SELECT * FROM `devices` WHERE `name` LIKE ? ORDER BY `created_at` DESC LIMIT ?, ?', $q, ($page - 1) * $per_page, $per_page);
    }
}
