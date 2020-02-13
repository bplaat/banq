<?php

class AdminAccountsController {
    public static function index () {
        $accounts = Accounts::select()->fetchAll();
        return view('admin.accounts.index', [ 'accounts' => $accounts ]);
    }

    public static function create () {
        $users = Users::select()->fetchAll();
        $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
        return view('admin.accounts.create', [ 'users' => $users, 'user_id' => $user_id ]);
    }

    public static function store () {
        $accounts = Accounts::select([ 'user_id' => $_POST['user_id'] ]);
        if (
            $accounts->rowCount() < Accounts::MAX_COUNT &&
            strlen($_POST['name']) >= Accounts::NAME_MIN_LENGTH &&
            strlen($_POST['name']) <= Accounts::NAME_MAX_LENGTH
        ) {
            Accounts::insert([
                'name' => $_POST['name'],
                'user_id' => $_POST['user_id'],
                'amount' => $_POST['amount'],
                'created_at' => date('Y-m-d H:i:s')
            ]);
            Router::redirect('/admin/accounts/' . Database::lastInsertId());
        } else {
            Router::back();
        }
    }

    public static function show ($account) {
        $account->user = Users::select($account->user_id)->fetch();
        $transactions = Transactions::selectAllByAccount($account->id)->fetchAll();
        foreach ($transactions as $transaction) {
            $transaction->from_account = Accounts::select($transaction->from_account_id)->fetch();
            $transaction->to_account = Accounts::select($transaction->to_account_id)->fetch();
        }
        return view('admin.accounts.show', [ 'account' => $account, 'transactions' => $transactions ]);
    }

    public static function edit ($account) {
        $users = Users::select()->fetchAll();
        return view('admin.accounts.edit', [ 'account' => $account, 'users' => $users ]);
    }

    public static function update ($account) {
        if (
            strlen($_POST['name']) >= Accounts::NAME_MIN_LENGTH &&
            strlen($_POST['name']) <= Accounts::NAME_MAX_LENGTH
        ) {
            Accounts::update($account->id, [
                'name' => $_POST['name'],
                'user_id' => $_POST['user_id'],
                'amount' => $_POST['amount']
            ]);
            Router::redirect('/admin/accounts/' . $account->id);
        } else {
            Router::back();
        }
    }

    public static function delete ($account) {
        Accounts::delete($account->id);
        Router::redirect('/admin/accounts');
    }
}
