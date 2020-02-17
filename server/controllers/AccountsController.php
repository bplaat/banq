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
        $_REQUEST['user_id'] = Auth::id();
        validate([
            'name' => Accounts::NAME_VALIDATION,
            'type' => Accounts::TYPE_VALIDATION,
            'user_id' => 'Accounts::MAX_COUNT_VALIDATION'
        ]);

        Accounts::insert([
            'name' => request('name'),
            'type' => request('type'),
            'user_id' => Auth::id(),
            'amount' => 0
        ]);
        Router::redirect('/accounts/' . Database::lastInsertId());
    }

    public static function show ($account) {
        $account->user = Users::select($account->user_id)->fetch();
        $page = request('page', 1);
        $per_page = 5;
        $last_page = ceil(Transactions::countAllByAccount($account->id) / $per_page);
        $transactions = Transactions::selectAllByAccount($account->id, $page, $per_page)->fetchAll();
        foreach ($transactions as $transaction) {
            $transaction->from_account = Accounts::select($transaction->from_account_id)->fetch();
            $transaction->to_account = Accounts::select($transaction->to_account_id)->fetch();
        }
        return view('accounts.show', [ 'account' => $account, 'transactions' => $transactions, 'page' => $page, 'last_page' => $last_page ]);
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
            validate([
                'name' => Accounts::NAME_VALIDATION,
                'type' => Accounts::TYPE_VALIDATION
            ]);

            Accounts::update($account->id, [
                'name' => request('name'),
                'type' => request('type')
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
