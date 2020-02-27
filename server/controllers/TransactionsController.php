<?php

class TransactionsController {
    // The transactions index page
    public static function index () {
        // The pagination vars
        $page = get_page();
        $per_page = 5;
        $last_page = ceil(Transactions::countByUser(Auth::id()) / $per_page);

        // Select all transactions by page and them accounts
        $transactions = Transactions::selectPageByUser(Auth::id(), $page, $per_page)->fetchAll();
        foreach ($transactions as $transaction) {
            $transaction->from_account = Accounts::select($transaction->from_account_id)->fetch();
            $transaction->to_account = Accounts::select($transaction->to_account_id)->fetch();
        }

        // Give all the data to the view
        return view('transactions.index', [
            'transactions' => $transactions,
            'page' => $page,
            'last_page' => $last_page
        ]);
    }

    // The transactions create page
    public static function create () {
        $from_accounts = Accounts::select([ 'user_id' => Auth::id() ])->fetchAll();
        $to_accounts = Accounts::select()->fetchAll();
        return view('transactions.create', [
            'from_accounts'=> $from_accounts,
            'to_accounts' => $to_accounts,
            'from_account_id' => request('from_account_id')
        ]);
    }

    // The transactions store page
    public static function store () {
        // Validate the users input
        validate([
            'name' => Transactions::NAME_VALIDATION,
            'from_account_id' => Transactions::FROM_ACCOUNT_ID_VALIDATION,
            'to_account_id' => Transactions::TO_ACCOUNT_ID_VALIDATION,
            'amount' => Transactions::AMOUNT_VALIDATION
        ]);

        // Parse the users amount
        $amount = parse_money_number(request('amount'));

        // Update the effected accounts
        $from_account = Accounts::select(request('from_account_id'))->fetch();
        $from_account->amount -= $amount;
        $to_account = Accounts::select(request('to_account_id'))->fetch();
        $to_account->amount += $amount;

        // Insert the transaction to the database
        Transactions::insert([
            'name' => request('name'),
            'from_account_id' => request('from_account_id'),
            'to_account_id' => request('to_account_id'),
            'amount' => $amount
        ]);
        $transaction_id = Database::lastInsertId();

        // Update the accounts in the database
        Accounts::update(request('from_account_id'), [ 'amount' => $from_account->amount ]);
        Accounts::update(request('to_account_id'), [ 'amount' => $to_account->amount ]);

        // Redirect to the transactions show page
        Router::redirect('/transactions/' . $transaction_id);
    }

    // The transactions show page
    public static function show ($transaction) {
        // Check if the transaction is of one of the accounts
        $transaction->from_account = Accounts::select($transaction->from_account_id)->fetch();
        $transaction->to_account = Accounts::select($transaction->to_account_id)->fetch();
        if ($transaction->from_account->user_id == Auth::id() || $transaction->to_account->user_id == Auth::id()) {
            return view('transactions.show', [ 'transaction' => $transaction ]);
        } else {
            // Else return 404 page
            return false;
        }
    }
}
