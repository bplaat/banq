<?php

class AccountsController {
    public static function index () {
        $accounts = Accounts::select([ 'user_id' => Auth::id() ]);
        return view('accounts.index', [ 'accounts' => $accounts ]);
    }

    public static function create () {
        return view('accounts.create');
    }

    public static function store () {
        Accounts::insert([
            'name' => $_POST['name'],
            'user_id' => Auth::id(),
            'amount' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        Router::redirect('/accounts/' . Database::lastInsertId());
    }

    public static function show ($account) {
        if ($account->user_id == Auth::id()) {
            return view('accounts.show', [ 'account' => $account ]);
        } else {
            return false;
        }
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
            Accounts::update($account->id, [
                'name' => $_POST['name']
            ]);
            Router::redirect('/accounts/' . $account->id);
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
