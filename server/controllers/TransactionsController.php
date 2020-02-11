<?php

class TransactionsController {
    public static function index () {
        $transactions = Transactions::selectAll(50)->fetchAll();
        foreach ($transactions as $transaction) {
            $transaction->from_account = Accounts::select($transaction->from_account_id)->fetch();
            $transaction->to_account = Accounts::select($transaction->to_account_id)->fetch();
        }
        return view('transactions.index', [ 'transactions' => $transactions ]);
    }

    public static function create () {
        $from_accounts = Accounts::select([ 'user_id' => Auth::id() ])->fetchAll();
        $to_accounts = Accounts::select()->fetchAll();
        $from_account_id = isset($_GET['from_account_id']) ? $_GET['from_account_id'] : '';
        return view('transactions.create', [
            'from_accounts'=> $from_accounts,
            'to_accounts' => $to_accounts,
            'from_account_id' => $from_account_id
        ]);
    }

    public static function store () {
        $from_account = Accounts::select($_POST['from_account_id'])->fetch();
        $from_new_amount = $from_account->amount - $_POST['amount'];

        if (
            $_POST['from_account_id'] != $_POST['to_account_id'] &&
            $from_account->user_id == Auth::id() &&
            $from_new_amount >= 0
        ) {
            Transactions::insert([
                'name' => $_POST['name'],
                'from_account_id' => $_POST['from_account_id'],
                'to_account_id' => $_POST['to_account_id'],
                'amount' => $_POST['amount'],
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $transaction_id = Database::lastInsertId();

            Accounts::update($_POST['from_account_id'], [
                'amount' => $from_new_amount
            ]);

            Accounts::update($_POST['to_account_id'], [
                'amount' => Accounts::select($_POST['to_account_id'])->fetch()->amount + $_POST['amount']
            ]);

            Router::redirect('/transactions/' . $transaction_id);
        } else {
            Router::back();
        }
    }

    public static function show ($transaction) {
        $transaction->from_account = Accounts::select($transaction->from_account_id)->fetch();
        $transaction->to_account = Accounts::select($transaction->to_account_id)->fetch();
        if ($transaction->from_account->user_id == Auth::id() || $transaction->to_account->user_id == Auth::id()) {
            return view('transactions.show', [ 'transaction' => $transaction ]);
        } else {
            return false;
        }
    }
}
