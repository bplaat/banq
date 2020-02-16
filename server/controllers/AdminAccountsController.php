<?php

class AdminAccountsController {
    public static function index () {
        $page = request('page', 1);
        $per_page = 9;
        $last_page = ceil(Accounts::count() / $per_page);
        $accounts = Accounts::selectPage($page, $per_page)->fetchAll();
        return view('admin.accounts.index', [ 'accounts' => $accounts, 'page' => $page, 'last_page' => $last_page ]);
    }

    public static function create () {
        $users = Users::select()->fetchAll();
        $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
        return view('admin.accounts.create', [ 'users' => $users, 'user_id' => $user_id ]);
    }

    public static function store () {
        validate([
            'name' => Accounts::NAME_VALIDATION,
            'user_id' => Accounts::USER_ID_VALIDATION,
            'amount' => Accounts::AMOUNT_VALIDATION,
            'user_id' => 'Accounts::MAX_COUNT_VALIDATION'
        ]);

        Accounts::insert([
            'name' => request('name'),
            'user_id' => request('user_id'),
            'amount' => request('amount')
        ]);

        Router::redirect('/admin/accounts/' . Database::lastInsertId());
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
        validate([
            'name' => Accounts::NAME_VALIDATION,
            'user_id' => Accounts::USER_ID_VALIDATION,
            'amount' => Accounts::AMOUNT_VALIDATION,
        ]);

        Accounts::update($account->id, [
            'name' => request('name'),
            'user_id' => request('user_id'),
            'amount' => request('amount')
        ]);

        Router::redirect('/admin/accounts/' . $account->id);
    }

    public static function delete ($account) {
        Accounts::delete($account->id);
        Router::redirect('/admin/accounts');
    }
}
