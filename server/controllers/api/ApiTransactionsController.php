<?php

class ApiTransactionsController {
    // The API transactions index route
    public static function index () {
        // The pagination vars
        $page = get_page();
        $limit = get_limit();
        $count = Transactions::count();

        // Select all the transactions by page
        $transactions = Transactions::selectPage($page, $limit)->fetchAll();

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'transactions' => $transactions
        ];
    }

    // The API transactions search route
    public static function search () {
        $q = request('q', '');

        // The pagination vars
        $page = get_page();
        $limit = get_limit();
        $count = Transactions::searchCount($q);

        // Select all the transactions by page
        $transactions = Transactions::searchPage($q, $page, $limit)->fetchAll();

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'transactions' => $transactions
        ];
    }

    // The API transactions create route
    public static function create () {
        // Validate the user input
        api_validate([
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

        // Return a confirmation message
        return [
            'message' => 'The transaction has been created successfully',
            'transaction_id' => $transaction_id
        ];
    }

    // The API transactions show route
    public static function show ($transaction) {
        return $transaction;
    }
}
