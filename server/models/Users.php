<?php

class Users extends Model {
    const FIRSTNAME_VALIDATION = 'required|min:2|max:35';
    const LASTNAME_VALIDATION = 'required|min:2|max:35';
    const USERNAME_VALIDATION = 'required|min:3|max:30|unique:Users';
    const USERNAME_EDIT_VALIDATION = 'required|min:3|max:30';
    const EMAIL_VALIDATION = 'required|email|max:255|unique:Users';
    const EMAIL_EDIT_VALIDATION = 'required|email|max:255';
    const PASSWORD_VALIDATION = 'required|min:6|max:255|confirmed';
    const PHONE_NUMBER_VALIDATION = 'required|min:6|max:32';
    const SEX_VALIDATION = 'required|size:1';
    const BIRTH_DATE_VALIDATION = 'required|date';
    const ADDRESS_VALIDATION = 'required|min:3|max:255';
    const POSTCODE_VALIDATION = 'required|min:6|max:32';
    const CITY_VALIDATION = 'required|min:2|max:255';
    const REGION_VALIDATION = 'required|min:2|max:255';
    const ROLE_VALIDATION = 'required|int|number_between:1,2';

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
            `phone_number` VARCHAR(32) NOT NULL,
            `sex` CHAR(1) NOT NULL,
            `birth_date` DATE NOT NULL,
            `address` VARCHAR(255) NOT NULL,
            `postcode` VARCHAR(32) NOT NULL,
            `city` VARCHAR(255) NOT NULL,
            `region` VARCHAR(255) NOT NULL,
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
            'phone_number' => '+31 6 4494 1181',
            'sex' => 'M',
            'birth_date' => '2002-07-01',
            'address' => 'Boschpolderstraat 27',
            'postcode' => '2807 LJ',
            'city' => 'Gouda',
            'region' => 'Zuid-Holland',
            'role' => static::ROLE_ADMIN
        ]);

        static::createUser([
            'firstname' => 'Jan',
            'lastname' => 'Jansen',
            'username' => 'jan',
            'email' => 'jan.jansen@gmail.com',
            'password' => password_hash('janjan', PASSWORD_DEFAULT),
            'phone_number' => '+31 6 4494 1181',
            'sex' => 'M',
            'birth_date' => '2002-07-01',
            'address' => 'Boschpolderstraat 27',
            'postcode' => '2807 LJ',
            'city' => 'Gouda',
            'region' => 'Zuid-Holland',
            'role' => static::ROLE_NORMAL
        ]);

        static::createUser([
            'firstname' => 'Deniz',
            'lastname' => 'Kahriman ',
            'username' => 'deniz',
            'email' => 'deniz@gmail.com',
            'password' => password_hash('deniz', PASSWORD_DEFAULT),
            'phone_number' => '+31 6 4494 1181',
            'sex' => 'M',
            'birth_date' => '2002-07-01',
            'address' => 'Boschpolderstraat 27',
            'postcode' => '2807 LJ',
            'city' => 'Gouda',
            'region' => 'Zuid-Holland',
            'role' => static::ROLE_ADMIN
        ]);

        static::createUser([
            'firstname' => 'Don',
            'lastname' => 'Luijendijk ',
            'username' => 'don',
            'email' => 'don@gmail.com',
            'password' => password_hash('don', PASSWORD_DEFAULT),
            'phone_number' => '+31 6 4494 1181',
            'sex' => 'M',
            'birth_date' => '2002-07-01',
            'address' => 'Boschpolderstraat 27',
            'postcode' => '2807 LJ',
            'city' => 'Gouda',
            'region' => 'Zuid-Holland',
            'role' => static::ROLE_ADMIN
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
