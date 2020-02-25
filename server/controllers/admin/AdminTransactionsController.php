<?php

class AdminTransactionsController {
    // The admin transactions index page
    public static function index () {
        // The pagination vars
        $page = get_page();
        $per_page = 9;

        // Check if search query is given
        if (request('q') != '') {
            $last_page = ceil(Transactions::searchCount(request('q')) / $per_page);
            $transactions = Transactions::searchPage(request('q'), $page, $per_page)->fetchAll();
        } else {
            $last_page = ceil(Transactions::count() / $per_page);
            $transactions = Transactions::selectPage($page, $per_page)->fetchAll();
        }

        // Select the accounts of every transaction
        foreach ($transactions as $transaction) {
            $transaction->from_account = Accounts::select($transaction->from_account_id)->fetch();
            $transaction->to_account = Accounts::select($transaction->to_account_id)->fetch();
        }

        // Give all the data to the view
        return view('admin.transactions.index', [
            'transactions' => $transactions,
            'page' => $page,
            'last_page' => $last_page
        ]);
    }

    // The admin transactions create page
    public static function create () {
        $accounts = Accounts::select()->fetchAll();
        return view('admin.transactions.create', [
            'accounts'=> $accounts,
            'from_account_id' => request('from_account_id')
        ]);
    }

    // The admin transactions store page
    public static function store () {
        // Validate the user input
        validate([
            'name' => Transactions::NAME_VALIDATION,
            'from_account_id' => Transactions::FROM_ACCOUNT_ID_ADMIN_VALIDATION,
            'to_account_id' => Transactions::TO_ACCOUNT_ID_VALIDATION,
            'amount' => Transactions::AMOUNT_VALIDATION
        ]);

        // Parse the amount
        $amount = parse_money_number(request('amount'));

        // Update both accounts
        $from_account = Accounts::select(request('from_account_id'))->fetch();
        $from_account->amount -= $amount;
        $to_account = Accounts::select(request('to_account_id'))->fetch();
        $to_account->amount += $amount;

        // Add the transaction to the database
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

        // Redirect to the new transactions show page
        Router::redirect('/admin/transactions/' . $transaction_id);
    }

    // The admin transactions show page
    public static function show ($transaction) {
        $transaction->from_account = Accounts::select($transaction->from_account_id)->fetch();
        $transaction->to_account = Accounts::select($transaction->to_account_id)->fetch();
        return view('admin.transactions.show', [ 'transaction' => $transaction ]);
    }
}
