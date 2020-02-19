<?php

class TransactionsController {
    public static function index () {
        $page = request('page', 1);
        $per_page = 5;
        $last_page = ceil(Transactions::countAllByUser() / $per_page);
        $transactions = Transactions::selectAllByUser($page, $per_page)->fetchAll();
        foreach ($transactions as $transaction) {
            $transaction->from_account = Accounts::select($transaction->from_account_id)->fetch();
            $transaction->to_account = Accounts::select($transaction->to_account_id)->fetch();
        }
        return view('transactions.index', [ 'transactions' => $transactions, 'page' => $page, 'last_page' => $last_page ]);
    }

    public static function create () {
        $from_accounts = Accounts::select([ 'user_id' => Auth::id() ])->fetchAll();
        $to_accounts = Accounts::select()->fetchAll();
        return view('transactions.create', [
            'from_accounts'=> $from_accounts,
            'to_accounts' => $to_accounts,
            'from_account_id' => request('from_account_id')
        ]);
    }

    public static function store () {
        validate([
            'name' => Transactions::NAME_VALIDATION,
            'from_account_id' => Transactions::FROM_ACCOUNT_ID_VALIDATION,
            'to_account_id' => Transactions::TO_ACCOUNT_ID_VALIDATION,
            'amount' => Transactions::AMOUNT_VALIDATION,
            'from_account_id' => 'Accounts::RIGHT_OWNER_VALIDATION',
            'from_account_id' => 'Accounts::ENOUGH_AMOUNT_VALIDATION'
        ]);

        $amount = parse_money_number(request('amount'));

        $from_account = Accounts::select(request('from_account_id'))->fetch();
        $from_account->amount -= $amount;
        $to_account = Accounts::select(request('to_account_id'))->fetch();
        $to_account->amount += $amount;

        Transactions::insert([
            'name' => request('name'),
            'from_account_id' => request('from_account_id'),
            'to_account_id' => request('to_account_id'),
            'amount' => $amount
        ]);
        $transaction_id = Database::lastInsertId();

        Accounts::update(request('from_account_id'), [ 'amount' => $from_account->amount ]);
        Accounts::update(request('to_account_id'), [ 'amount' => $to_account->amount ]);

        Router::redirect('/transactions/' . $transaction_id);
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
