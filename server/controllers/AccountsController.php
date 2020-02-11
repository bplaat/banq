<?php

class AccountsController {
    public static function index () {
        $accounts = Accounts::select([ 'user_id' => Auth::id() ])->fetchAll();
        return view('accounts.index', [ 'accounts' => $accounts ]);
    }

    public static function create () {
        return view('accounts.create');
    }

    public static function store () {
        $accounts = Accounts::select([ 'user_id' => Auth::id() ]);
        if (
            $accounts->rowCount() < Accounts::MAX_COUNT &&
            strlen($_POST['name']) >= Accounts::NAME_MIN_LENGTH &&
            strlen($_POST['name']) <= Accounts::NAME_MAX_LENGTH
        ) {
            Accounts::insert([
                'name' => $_POST['name'],
                'user_id' => Auth::id(),
                'amount' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            Router::redirect('/accounts/' . Database::lastInsertId());
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
        return view('accounts.show', [ 'account' => $account, 'transactions' => $transactions ]);
    }

    public static function edit ($account) {
        if ($account->user_id == Auth::id()) {
            return view('accounts.edit', [ 'account' => $account ]);
        } else {
            return false;
        }
    }

    public static function update ($account) {
        if ($account->user_id == Auth::id()) {
            if (
                strlen($_POST['name']) >= Accounts::NAME_MIN_LENGTH &&
                strlen($_POST['name']) <= Accounts::NAME_MAX_LENGTH
            ) {
                Accounts::update($account->id, [
                    'name' => $_POST['name']
                ]);
                Router::redirect('/accounts/' . $account->id);
            } else {
                Router::back();
            }
        } else {
            return false;
        }
    }

    public static function delete ($account) {
        if ($account->user_id == Auth::id()) {
            Accounts::delete($account->id);
            Router::redirect('/accounts');
        } else {
            return false;
        }
    }
}
