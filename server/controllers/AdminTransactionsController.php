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
        $from_account = Accounts::select($_POST['from_account_id'])->fetch();
        $from_account->amount -= $_POST['amount'];
        $to_account = Accounts::select($_POST['to_account_id'])->fetch();
        $to_account->amount += $_POST['amount'];

        if (
            strlen($_POST['name']) >= Transactions::NAME_MIN_LENGTH &&
            strlen($_POST['name']) <= Transactions::NAME_MAX_LENGTH &&
            $_POST['amount'] > 0 &&
            $_POST['from_account_id'] != $_POST['to_account_id'] &&
            $from_account->amount >= 0
        ) {
            Transactions::insert([
                'name' => $_POST['name'],
                'from_account_id' => $_POST['from_account_id'],
                'to_account_id' => $_POST['to_account_id'],
                'amount' => $_POST['amount'],
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $transaction_id = Database::lastInsertId();
            Accounts::update($_POST['from_account_id'], [ 'amount' => $from_account->amount ]);
            Accounts::update($_POST['to_account_id'], [ 'amount' => $to_account->amount ]);
            Router::redirect('/admin/transactions/' . $transaction_id);
        } else {
            Router::back();
        }
    }

    public static function show ($transaction) {
        $transaction->from_account = Accounts::select($transaction->from_account_id)->fetch();
        $transaction->to_account = Accounts::select($transaction->to_account_id)->fetch();
        return view('admin.transactions.show', [ 'transaction' => $transaction ]);
    }
}
