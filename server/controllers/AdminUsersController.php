<?php

class AdminUsersController {
    public static function index () {
        $users = Users::select()->fetchAll();
        return view('admin.users.index', [ 'users' => $users ]);
    }

    public static function create () {
        return view('admin.users.create');
    }

    public static function store () {
        $user_query = Users::selectByLogin($_POST['username'], $_POST['email']);
        if (
            strlen($_POST['firstname']) >= Users::FIRSTNAME_MIN_LENGTH &&
            strlen($_POST['firstname']) <= Users::FIRSTNAME_MAX_LENGTH &&
            strlen($_POST['lastname']) >= Users::LASTNAME_MIN_LENGTH &&
            strlen($_POST['lastname']) <= Users::LASTNAME_MAX_LENGTH &&
            strlen($_POST['username']) >= Users::USERNAME_MIN_LENGTH &&
            strlen($_POST['username']) <= Users::USERNAME_MAX_LENGTH &&
            strlen($_POST['email']) <= Users::EMAIL_MAX_LENGTH &&
            filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) &&
            $user_query->rowCount() == 0 &&
            strlen($_POST['password']) >= Users::PASSWORD_MIN_LENGTH &&
            strlen($_POST['password']) <= Users::PASSWORD_MAX_LENGTH &&
            $_POST['password'] == $_POST['confirm_password']
        ) {
            Users::insert([
                'firstname' => $_POST['firstname'],
                'lastname' => $_POST['lastname'],
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'role' => $_POST['role'],
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $user_id = Database::lastInsertId();

            Accounts::insert([
                'name' => $_POST['firstname'] . '\'s Account',
                'user_id' => $user_id,
                'amount' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            Router::redirect('/admin/users/' . $user_id);
        }
        Router::back();
    }

    public static function show ($user) {
        $accounts = Accounts::select([ 'user_id' => $user->id ])->fetchAll();
        return view('admin.users.show', [ 'user' => $user, 'accounts' => $accounts ]);
    }

    public static function edit ($user) {
        return view('admin.users.edit', [ 'user' => $user ]);
    }

    public static function update ($user) {
        if (
            strlen($_POST['firstname']) >= Users::FIRSTNAME_MIN_LENGTH &&
            strlen($_POST['firstname']) <= Users::FIRSTNAME_MAX_LENGTH &&
            strlen($_POST['lastname']) >= Users::LASTNAME_MIN_LENGTH &&
            strlen($_POST['lastname']) <= Users::LASTNAME_MAX_LENGTH &&
            strlen($_POST['username']) >= Users::USERNAME_MIN_LENGTH &&
            strlen($_POST['username']) <= Users::USERNAME_MAX_LENGTH &&
            strlen($_POST['email']) <= Users::EMAIL_MAX_LENGTH &&
            filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)
        ) {
            Users::update($user->id, [
                'firstname' => $_POST['firstname'],
                'lastname' => $_POST['lastname'],
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'role' => $_POST['role']
            ]);

            if ($_POST['password'] != '') {
                if (
                    strlen($_POST['password']) >= Users::PASSWORD_MIN_LENGTH &&
                    strlen($_POST['password']) <= Users::PASSWORD_MAX_LENGTH &&
                    $_POST['password'] == $_POST['confirm_password']
                ) {
                    Users::update($user->id, [
                        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
                    ]);
                    Router::redirect('/admin/users/' . $user->id);
                }
                Router::back();
            }

            Router::redirect('/admin/users/' . $user->id);
        }
        Router::back();
    }

    public static function delete ($user) {
        Users::deleteComplete($user->id);
        Router::redirect('/admin/users');
    }
}
