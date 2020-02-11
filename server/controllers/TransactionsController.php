<?php

class TransactionsController {
    public static function index () {
        $transactions = Transactions::selectAll(50)->fetchAll();
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
        $from_new_amount = Accounts::select($_POST['from_account_id'])->fetch()->amount - $_POST['amount'];
        if ($from_new_amount >= 0) {
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
        $from_account = Accounts::select($transaction->from_account_id)->fetch();
        $to_account = Accounts::select($transaction->to_account_id)->fetch();
        if ($from_account->user_id == Auth::id() || $to_account->user_id == Auth::id()) {
            return view('transactions.show', [ 'transaction' => $transaction ]);
        } else {
            return false;
        }
    }
}
