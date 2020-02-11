<?php

class Users extends Model {
    public static function create () {
        return Database::query('CREATE TABLE `users` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `firstname` VARCHAR(191) NOT NULL,
            `lastname` VARCHAR(191) NOT NULL,
            `username` VARCHAR(191) UNIQUE NOT NULL,
            `email` VARCHAR(191) UNIQUE NOT NULL,
            `password` VARCHAR(191) NOT NULL,
            `created_at` DATETIME NOT NULL
        )');
    }

    public static function fill () {
        static::insert([
            'firstname' => 'Bastiaan',
            'lastname' => 'van der Plaat',
            'username' => 'bplaat',
            'email' => 'bastiaan.v.d.plaat@gmail.com',
            'password' => password_hash('gouda', PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public static function selectByLogin ($username, $email) {
        return Database::query('SELECT * FROM `users` WHERE `username` = ? OR `email` = ?', $username, $email);
    }
}
