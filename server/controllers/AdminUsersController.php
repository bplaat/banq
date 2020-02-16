<?php

class AdminUsersController {
    public static function index () {
        $page = request('page', 1);
        $per_page = 9;
        $last_page = ceil(Users::count() / $per_page);
        $users = Users::selectPage($page, $per_page)->fetchAll();
        return view('admin.users.index', [ 'users' => $users, 'page' => $page, 'last_page' => $last_page ]);
    }

    public static function create () {
        return view('admin.users.create');
    }

    public static function store () {
        validate([
            'firstname' => Users::FIRSTNAME_VALIDATION,
            'lastname' => Users::LASTNAME_VALIDATION,
            'username' => Users::USERNAME_VALIDATION,
            'email' => Users::EMAIL_VALIDATION,
            'password' => Users::PASSWORD_VALIDATION,
            'role' => Users::ROLE_VALIDATION
        ]);

        $user_id = Users::createUser([
            'firstname' => request('firstname'),
            'lastname' => request('lastname'),
            'username' => request('username'),
            'email' => request('email'),
            'password' => password_hash(request('password'), PASSWORD_DEFAULT),
            'role' => request('role')
        ]);

        Router::redirect('/admin/users/' . $user_id);
    }

    public static function show ($user) {
        $accounts = Accounts::select([ 'user_id' => $user->id ])->fetchAll();
        return view('admin.users.show', [ 'user' => $user, 'accounts' => $accounts ]);
    }

    public static function edit ($user) {
        return view('admin.users.edit', [ 'user' => $user ]);
    }

    public static function update ($user) {
        validate([
            'firstname' => Users::FIRSTNAME_VALIDATION,
            'lastname' => Users::LASTNAME_VALIDATION,
            'username' => Users::USERNAME_EDIT_VALIDATION,
            'email' => Users::EMAIL_EDIT_VALIDATION,
            'role' => Users::ROLE_VALIDATION
        ]);

        if (request('password') != '') {
            validate([
                'password' => Users::PASSWORD_VALIDATION,
            ]);
        }

        Users::update($user->id, [
            'firstname' => request('firstname'),
            'lastname' => request('lastname'),
            'username' => request('username'),
            'email' => request('email'),
            'role' => request('role')
        ]);

        if (request('password') != '') {
            Users::update($user->id, [
                'password' => password_hash(request('password'), PASSWORD_DEFAULT)
            ]);
        }

        Router::redirect('/admin/users/' . $user->id);
    }

    public static function delete ($user) {
        Users::deleteUser($user->id);
        Router::redirect('/admin/users');
    }
}
