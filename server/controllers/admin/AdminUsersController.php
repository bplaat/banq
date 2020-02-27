<?php

class AdminUsersController {
    // The admin users index page
    public static function index () {
        // The pagination vars
        $page = get_page();
        $per_page = 9;

        // Check if search query is given
        if (request('q') != '') {
            $last_page = ceil(Users::searchCount(request('q')) / $per_page);
            $users = Users::searchSelectPage(request('q'), $page, $per_page)->fetchAll();
        } else {
            $last_page = ceil(Users::count() / $per_page);
            $users = Users::selectPage($page, $per_page)->fetchAll();
        }

        // Give all data to the view
        return view('admin.users.index', [
            'users' => $users,
            'page' => $page,
            'last_page' => $last_page
        ]);
    }

    // The admin users create page
    public static function create () {
        return view('admin.users.create');
    }

    // The admin users store page
    public static function store () {
        // Validate the user input fields
        validate([
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

        // Redirect to the new users show page
        Router::redirect('/admin/users/' . $user_id);
    }

    // The admin users show page
    public static function show ($user) {
        $accounts = Accounts::select([ 'user_id' => $user->id ])->fetchAll();
        return view('admin.users.show', [
            'user' => $user,
            'accounts' => $accounts
        ]);
    }

    // The admin users edit page
    public static function edit ($user) {
        return view('admin.users.edit', [ 'user' => $user ]);
    }

    // The admin users update page
    public static function update ($user) {
        // Validate the user input fields
        validate([
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
            validate([
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

        // Redirect to the users show page
        Router::redirect('/admin/users/' . $user->id);
    }

    // The admin users delete page
    public static function delete ($user) {
        Users::deleteUser($user->id);
        Router::redirect('/admin/users');
    }
}
