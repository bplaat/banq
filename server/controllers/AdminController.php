<?php

class AdminController {
    public static function index () {
        $users = Users::select()->fetchAll();
        return view('admin.index', [ 'users' => $users ]);
    }

    public static function usersShow ($user) {
        $accounts = Accounts::select([ 'user_id' => $user->id ])->fetchAll();
        return view('admin.users.show', [ 'user' => $user, 'accounts' => $accounts ]);
    }

    public static function accountsShow ($account) {
        $transactions = Transactions::selectAllByAccount($account->id)->fetchAll();
        foreach ($transactions as $transaction) {
            $transaction->from_account = Accounts::select($transaction->from_account_id)->fetch();
            $transaction->to_account = Accounts::select($transaction->to_account_id)->fetch();
        }
        return view('admin.accounts.show', [ 'account' => $account, 'transactions' => $transactions ]);
    }
}
