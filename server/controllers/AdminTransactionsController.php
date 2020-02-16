<?php

class AdminTransactionsController {
    public static function index () {
        $transactions = Transactions::select()->fetchAll();
        foreach ($transactions as $transaction) {
            $transaction->from_account = Accounts::select($transaction->from_account_id)->fetch();
            $transaction->to_account = Accounts::select($transaction->to_account_id)->fetch();
        }
        return view('admin.transactions.index', [ 'transactions' => $transactions ]);
    }

    public static function create () {
        $accounts = Accounts::select()->fetchAll();
        $from_account_id = isset($_GET['from_account_id']) ? $_GET['from_account_id'] : '';
        return view('admin.transactions.create', [
            'accounts'=> $accounts,
            'from_account_id' => $from_account_id
        ]);
    }

    public static function store () {
        validate([
            'name' => Transactions::NAME_VALIDATION,
            'from_account_id' => Transactions::FROM_ACCOUNT_ID_VALIDATION,
            'to_account_id' => Transactions::TO_ACCOUNT_ID_VALIDATION,
            'amount' => Transactions::AMOUNT_VALIDATION,
            'from_account_id' => 'Transactions::ENOUGH_AMOUNT_VALIDATION'
        ]);

        $from_account = Accounts::select(request('from_account_id'))->fetch();
        $from_account->amount -= request('amount');
        $to_account = Accounts::select(request('to_account_id'))->fetch();
        $to_account->amount += request('amount');

        Transactions::insert([
            'name' => request('name'),
            'from_account_id' => request('from_account_id'),
            'to_account_id' => request('to_account_id'),
            'amount' => request('amount')
        ]);
        $transaction_id = Database::lastInsertId();

        Accounts::update(request('from_account_id'), [ 'amount' => $from_account->amount ]);
        Accounts::update(request('to_account_id'), [ 'amount' => $to_account->amount ]);

        Router::redirect('/admin/transactions/' . $transaction_id);
    }

    public static function show ($transaction) {
        $transaction->from_account = Accounts::select($transaction->from_account_id)->fetch();
        $transaction->to_account = Accounts::select($transaction->to_account_id)->fetch();
        return view('admin.transactions.show', [ 'transaction' => $transaction ]);
    }
}