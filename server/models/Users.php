<?php

class Users extends Model {
    // The validation rules for all the fields
    const FIRSTNAME_VALIDATION = 'required|min:2|max:35';
    const LASTNAME_VALIDATION = 'required|min:2|max:35';
    const USERNAME_VALIDATION = 'required|min:3|max:30|unique:Users';
    const USERNAME_EDIT_VALIDATION = 'required|min:3|max:30';
    const EMAIL_VALIDATION = 'required|email|max:255|unique:Users';
    const EMAIL_EDIT_VALIDATION = 'required|email|max:255';
    const OLD_PASSWORD_VALIDATION = '@Users::VERIFY_PASSWORD_VALIDATION';
    const PASSWORD_VALIDATION = 'required|min:6|max:255|confirmed';
    const PHONE_NUMBER_VALIDATION = 'required|min:6|max:32';
    const SEX_VALIDATION = 'required|size:1';
    const BIRTH_DATE_VALIDATION = 'required|date';
    const ADDRESS_VALIDATION = 'required|min:3|max:255';
    const POSTCODE_VALIDATION = 'required|min:6|max:32';
    const CITY_VALIDATION = 'required|min:2|max:255';
    const REGION_VALIDATION = 'required|min:2|max:255';
    const ROLE_VALIDATION = 'required|int|number_between:1,2';

    // The custom validation which checks and old password
    public static function VERIFY_PASSWORD_VALIDATION ($key, $value) {
        if (!password_verify($value, Auth::user()->password)) {
            return 'The field ' . $key . ' must contain your current password';
        }
    }

    // The user roles
    const ROLE_NORMAL = 1;
    const ROLE_ADMIN = 2;

    // The users create table function
    public static function create () {
        return Database::query('CREATE TABLE `users` (
            `id` INT UNSIGNED AUTO_INCREMENT,
            `firstname` VARCHAR(255) NOT NULL,
            `lastname` VARCHAR(255) NOT NULL,
            `username` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) NOT NULL,
            `password` VARCHAR(255) NOT NULL,
            `phone_number` VARCHAR(32) NOT NULL,
            `sex` CHAR(1) NOT NULL,
            `birth_date` DATE NOT NULL,
            `address` VARCHAR(255) NOT NULL,
            `postcode` VARCHAR(32) NOT NULL,
            `city` VARCHAR(255) NOT NULL,
            `region` VARCHAR(255) NOT NULL,
            `role` INT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE (`username`),
            UNIQUE (`email`)
        )');
    }

    // A custom query function select user by username or email
    public static function selectByLogin ($username, $email) {
        return Database::query('SELECT * FROM `users` WHERE `username` = ? OR `email` = ?', $username, $email);
    }

    // A custom query function count users by search query
    public static function searchCount ($q) {
        $q = '%' . $q . '%';
        return Database::query('SELECT COUNT(*) FROM `users` WHERE `firstname` LIKE ? OR `lastname` LIKE ? OR `username` LIKE ? OR `email` LIKE ?', $q, $q, $q, $q)->fetch()->{'COUNT(*)'};
    }

    // A custom query function paged select users by search query
    public static function searchSelectPage ($q, $page, $per_page) {
        $q = '%' . $q . '%';
        return Database::query('SELECT * FROM `users` WHERE `firstname` LIKE ? OR `lastname` LIKE ? OR `username` LIKE ? OR `email` LIKE ? ORDER BY `created_at` DESC LIMIT ?, ?', $q, $q, $q, $q, ($page - 1) * $per_page, $per_page);
    }

    // A function to create a user and two standard accounts
    public static function createUser ($user) {
        // Insert the user in the users table
        static::insert($user);

        // Get the new user id
        $user_id = Database::lastInsertId();

        // Create a new users save account
        Accounts::insert([
            'name' => $user['firstname'] . '\'s Save Account',
            'type' => Accounts::TYPE_SAVE,
            'user_id' => $user_id,
            'amount' => 50
        ]);

        // Create a new users payment account
        Accounts::insert([
            'name' => $user['firstname'] . '\'s Payment Account',
            'type' => Accounts::TYPE_PAYMENT,
            'user_id' => $user_id,
            'amount' => 0
        ]);

        // Return the new users id
        return $user_id;
    }

    // A function to delete a user and all it dependencies
    public static function deleteUser ($user_id) {
        // Delete all the accounts of the user
        Accounts::delete([ 'user_id' => $user_id ]);

        // Delete all the sessions of the user
        Sessions::delete([ 'user_id' => $user_id ]);

        // Delete the user
        Users::delete($user_id);
    }
}
