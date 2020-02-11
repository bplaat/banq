<?php

class Users extends Model {
    const FIRSTNAME_MIN_LENGTH = 2;
    const FIRSTNAME_MAX_LENGTH = 20;
    const LASTNAME_MIN_LENGTH = 2;
    const LASTNAME_MAX_LENGTH = 30;
    const USERNAME_MIN_LENGTH = 3;
    const USERNAME_MAX_LENGTH = 20;
    const EMAIL_MAX_LENGTH = 191;
    const PASSWORD_MIN_LENGTH = 6;
    const PASSWORD_MAX_LENGTH = 256;

    const ROLE_NORMAL = 1;
    const ROLE_ADMIN = 2;

    public static function create () {
        return Database::query('CREATE TABLE `users` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `firstname` VARCHAR(' . FIRSTNAME_MAX_LENGTH . ') NOT NULL,
            `lastname` VARCHAR(' . LASTNAME_MAX_LENGTH . ') NOT NULL,
            `username` VARCHAR(' . USERNAME_MAX_LENGTH . ') UNIQUE NOT NULL,
            `email` VARCHAR(' . EMAIL_MAX_LENGTH . ') UNIQUE NOT NULL,
            `password` VARCHAR(191) NOT NULL,
            `role` TINYINT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL
        )');
    }

    public static function fill () {
        static::insert([
            'firstname' => 'Bastiaan',
            'lastname' => 'van der Plaat',
            'username' => 'bplaat',
            'email' => 'bastiaan.v.d.plaat@gmail.com',
            'password' => '$2y$10$Pm3Ewd1bIOldyRQudyGpR.HG7a3VpKEJgNfmswo.jhwpKtHh40FrO',
            'role' => static::ROLE_ADMIN,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        static::insert([
            'firstname' => 'Jan',
            'lastname' => 'Jansen',
            'username' => 'jan',
            'email' => 'jan.jansen@gmail.com',
            'password' => '$2y$10$Eed5f4pTWbRANwFQGchi/.j3Qi0vvdHZ6zQmGIxhOhW1eEyo2iEOq',
            'role' => static::ROLE_NORMAL,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public static function selectByLogin ($username, $email) {
        return Database::query('SELECT * FROM `users` WHERE `username` = ? OR `email` = ?', $username, $email);
    }
}
