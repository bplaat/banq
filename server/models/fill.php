<?php

// The post migration fills

// Create Banq admin user
Users::insert([
    'firstname' => 'Banq Admin',
    'lastname' => '',
    'username' => 'admin',
    'email' => 'admin@banq.ml',
    'password' => '$2y$10$Pm3Ewd1bIOldyRQudyGpR.HG7a3VpKEJgNfmswo.jhwpKtHh40FrO',
    'phone_number' => '+31 6 1234 1234',
    'sex' => 'M',
    'birth_date' => '2001-10-13',
    'address' => 'Steenstraat 32',
    'postcode' => '1234 AB',
    'city' => 'Gouda',
    'region' => 'Zuid-Holland',
    'role' => Users::ROLE_ADMIN
]);

// Create Banq withdraw account
Accounts::insert([
    'id' => ADMIN_WITHDRAW_ACCOUNT_ID,
    'name' => 'Banq Withdraw Account',
    'type' => Accounts::TYPE_SAVE,
    'user_id' => 1,
    'amount' => 0
]);

// Create Banq interest account
Accounts::insert([
    'id' => ADMIN_INTEREST_ACCOUNT_ID,
    'name' => 'Banq Interest Account',
    'type' => Accounts::TYPE_SAVE,
    'user_id' => 1,
    'amount' => 0
]);

// Create Banq deleted account
Accounts::insert([
    'id' => ADMIN_DELETED_ACCOUNT_ID,
    'name' => 'Banq Deleted Account',
    'type' => Accounts::TYPE_SAVE,
    'user_id' => 1,
    'amount' => 0
]);

// Create Bastiaan admin user
Users::createUser([
    'firstname' => 'Bastiaan',
    'lastname' => 'van der Plaat',
    'username' => 'bplaat',
    'email' => 'bastiaan.v.d.plaat@gmail.com',
    'password' => '$2y$10$Pm3Ewd1bIOldyRQudyGpR.HG7a3VpKEJgNfmswo.jhwpKtHh40FrO',
    'phone_number' => '+31 6 1234 1234',
    'sex' => 'M',
    'birth_date' => '2001-10-13',
    'address' => 'Steenstraat 32',
    'postcode' => '1234 AB',
    'city' => 'Gouda',
    'region' => 'Zuid-Holland',
    'role' => Users::ROLE_ADMIN
]);

// Create Deniz admin user
Users::createUser([
    'firstname' => 'Deniz',
    'lastname' => 'Kahriman ',
    'username' => 'deniz',
    'email' => 'denizik12@hotmail.com',
    'password' => '$2y$10$ERclTmiB73tL4PCAMz3/w.iYibh/3zEsVVitMlSVcZCHn921yE.em',
    'phone_number' => '+31 6 4774 1181',
    'sex' => 'M',
    'birth_date' => '2001-08-01',
    'address' => 'Mariastraat 27',
    'postcode' => '3857 KL',
    'city' => 'Rotterdam',
    'region' => 'Zuid-Holland',
    'role' => Users::ROLE_ADMIN
]);

// Create Don admin user
Users::createUser([
    'firstname' => 'Don',
    'lastname' => 'Luijendijk ',
    'username' => 'don',
    'email' => 'don@mail.com',
    'password' => '$2y$10$PpwUe0xSRkhbYnVOQVC9s.PJA8fl8UIU0lS6p2f6n0F4osrIiY.I2',
    'phone_number' => '+31 6 1234 5678',
    'sex' => 'M',
    'birth_date' => '2000-01-01',
    'address' => 'Straat 1',
    'postcode' => '1234 AB',
    'city' => 'Rotterdam',
    'region' => 'Zuid-Holland',
    'role' => Users::ROLE_ADMIN
]);

// Create Jan (test) user
Users::createUser([
    'firstname' => 'Jan',
    'lastname' => 'Jansen',
    'username' => 'jan',
    'email' => 'jan.jansen@gmail.com',
    'password' => password_hash('janjan', PASSWORD_DEFAULT),
    'phone_number' => '+31 6 1234 1234',
    'sex' => 'M',
    'birth_date' => '2001-10-13',
    'address' => 'Steenstraat 32',
    'postcode' => '1234 AB',
    'city' => 'Gouda',
    'region' => 'Zuid-Holland',
    'role' => Users::ROLE_NORMAL
]);

// Create a API test device
Devices::insert([
    'name' => 'API Test Device',
    'key' => Devices::generateKey()
]);
