<?php

class Users extends Model {
    const FIRSTNAME_VALIDATION = 'required|min:2|max:35';
    const LASTNAME_VALIDATION = 'required|min:2|max:35';
    const USERNAME_VALIDATION = 'required|min:3|max:30|unique:Users';
    const USERNAME_EDIT_VALIDATION = 'required|min:3|max:30';
    const EMAIL_VALIDATION = 'required|email|max:255|unique:Users';
    const EMAIL_EDIT_VALIDATION = 'required|email|max:255';
    const PASSWORD_VALIDATION = 'required|min:6|max:255|confirmed';
    const ROLE_VALIDATION = 'required|int|digits_between:1,2';

    public static function VERIFY_PASSWORD_VALIDATION ($key, $value) {
        if (!password_verify($value, Auth::user()->password)) {
            return 'The field ' . $key . ' must contain your current password';
        }
    }

    const ROLE_NORMAL = 1;
    const ROLE_ADMIN = 2;

    public static function create () {
        return Database::query('CREATE TABLE `users` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `firstname` VARCHAR(255) NOT NULL,
            `lastname` VARCHAR(255) NOT NULL,
            `username` VARCHAR(255) UNIQUE NOT NULL,
            `email` VARCHAR(255) UNIQUE NOT NULL,
            `password` VARCHAR(255) NOT NULL,
            `role` INT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )');
    }

    public static function fill () {
        static::createUser([
            'firstname' => 'Bastiaan',
            'lastname' => 'van der Plaat',
            'username' => 'bplaat',
            'email' => 'bastiaan.v.d.plaat@gmail.com',
            'password' => '$2y$10$Pm3Ewd1bIOldyRQudyGpR.HG7a3VpKEJgNfmswo.jhwpKtHh40FrO',
            'role' => static::ROLE_ADMIN
        ]);

        static::createUser([
            'firstname' => 'Jan',
            'lastname' => 'Jansen',
            'username' => 'jan',
            'email' => 'jan.jansen@gmail.com',
            'password' => '$2y$10$Eed5f4pTWbRANwFQGchi/.j3Qi0vvdHZ6zQmGIxhOhW1eEyo2iEOq',
            'role' => static::ROLE_NORMAL
        ]);
    }

    public static function selectByLogin ($username, $email) {
        return Database::query('SELECT * FROM `users` WHERE `username` = ? OR `email` = ?', $username, $email);
    }

    public static function createUser ($user) {
        static::insert($user);

        $user_id = Database::lastInsertId();

        Accounts::insert([
            'name' => $user['firstname'] . '\'s Save Account',
            'user_id' => $user_id,
            'amount' => 50
        ]);

        Accounts::insert([
            'name' => $user['firstname'] . '\'s Payment Account',
            'user_id' => $user_id,
            'amount' => 0
        ]);

        return $user_id;
    }

    public static function deleteUser ($user_id) {
        Accounts::delete([ 'user_id' => $user_id ]);
        Sessions::delete([ 'user_id' => $user_id ]);
        Users::delete($user_id);
    }
}
