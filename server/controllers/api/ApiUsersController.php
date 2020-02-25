<?php

class ApiUsersController {
    // The API users index route
    public static function index () {
        // The pagination vars
        $page = get_page();
        $limit = get_limit();
        $count = Users::count();

        // Select all the users by page
        $users = Users::selectPage($page, $limit)->fetchAll();
        foreach ($users as $user) {
            unset($user->password);
        }

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'users' => $users
        ];
    }

    // The API users search route
    public static function search () {
        $q = request('q', '');

        // The pagination vars
        $page = get_page();
        $limit = get_limit();
        $count = Users::searchCount($q);

        // Select all the users by page
        $users = Users::searchPage($q, $page, $limit)->fetchAll();
        foreach ($users as $user) {
            unset($user->password);
        }

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'users' => $users
        ];
    }

    // The API users create route
    public static function create () {
        // Validate the user input fields
        api_validate([
            'firstname' => Users::FIRSTNAME_VALIDATION,
            'lastname' => Users::LASTNAME_VALIDATION,
            'username' => Users::USERNAME_VALIDATION,
            'email' => Users::EMAIL_VALIDATION,
            'password' => Users::PASSWORD_VALIDATION,
            'phone_number' => USERS::PHONE_NUMBER_VALIDATION,
            'sex' => USERS::SEX_VALIDATION,
            'birth_date' => USERS::BIRTH_DATE_VALIDATION,
            'address' => USERS::ADDRESS_VALIDATION,
            'postcode' => USERS::POSTCODE_VALIDATION,
            'city' => USERS::CITY_VALIDATION,
            'region' => USERS::REGION_VALIDATION,
            'role' => Users::ROLE_VALIDATION
        ]);

        // Create the user
        $user_id = Users::createUser([
            'firstname' => request('firstname'),
            'lastname' => request('lastname'),
            'username' => request('username'),
            'email' => request('email'),
            'password' => password_hash(request('password'), PASSWORD_DEFAULT),
            'phone_number' => request('phone_number'),
            'sex' => request('sex'),
            'birth_date' => request('birth_date'),
            'address' => request('address'),
            'postcode' => request('postcode'),
            'city' => request('city'),
            'region' => request('region'),
            'role' => request('role')
        ]);

        // Return a conformation message
        return [
            'message' => 'The user has been created successfully',
            'user_id' => $user_id
        ];
    }

    // The API users show route
    public static function show ($user) {
        unset($user->password);
        return $user;
    }

    // The API users edit route
    public static function edit ($user) {
        // Validate the user input fields
        api_validate([
            'firstname' => Users::FIRSTNAME_VALIDATION,
            'lastname' => Users::LASTNAME_VALIDATION,
            'username' => Users::USERNAME_EDIT_VALIDATION,
            'email' => Users::EMAIL_EDIT_VALIDATION,
            'phone_number' => USERS::PHONE_NUMBER_VALIDATION,
            'sex' => USERS::SEX_VALIDATION,
            'birth_date' => USERS::BIRTH_DATE_VALIDATION,
            'address' => USERS::ADDRESS_VALIDATION,
            'postcode' => USERS::POSTCODE_VALIDATION,
            'city' => USERS::CITY_VALIDATION,
            'region' => USERS::REGION_VALIDATION,
            'role' => Users::ROLE_VALIDATION
        ]);

        // Check the password if providate
        if (request('password') != '') {
            api_validate([
                'password' => Users::PASSWORD_VALIDATION,
            ]);
        }

        // Update the user details
        Users::update($user->id, [
            'firstname' => request('firstname'),
            'lastname' => request('lastname'),
            'username' => request('username'),
            'email' => request('email'),
            'phone_number' => request('phone_number'),
            'sex' => request('sex'),
            'birth_date' => request('birth_date'),
            'address' => request('address'),
            'postcode' => request('postcode'),
            'city' => request('city'),
            'region' => request('region'),
            'role' => request('role')
        ]);

        // If the password if provided
        if (request('password') != '') {
            Users::update($user->id, [
                'password' => password_hash(request('password'), PASSWORD_DEFAULT)
            ]);
        }

        // Return a confirmation message
        return [
            'message' => 'The user has been edited successfully'
        ];
    }

    // The API users delete route
    public static function delete ($user) {
        // Delete the user
        Users::deleteUser($user->id);

        // Return a confirmation message
        return [
            'message' => 'The user has been deleted successfully'
        ];
    }
}
