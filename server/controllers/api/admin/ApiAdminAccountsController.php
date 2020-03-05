<?php

class ApiAdminAccountsController {
    // The API admin accounts index route
    public static function index () {
        // The pagination vars
        $page = get_page();
        $limit = get_limit();
        $count = Accounts::count();

        // Select all the accounts by page
        $accounts = Accounts::selectPage($page, $limit)->fetchAll();

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'accounts' => $accounts
        ];
    }

    // The API admin accounts search route
    public static function search () {
        $q = request('q', '');

        // The pagination vars
        $page = get_page();
        $limit = get_limit();
        $count = Accounts::searchCount($q);

        // Select all the accounts by page
        $accounts = Accounts::searchSelectPage($q, $page, $limit)->fetchAll();

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'accounts' => $accounts
        ];
    }

    // The API admin accounts create route
    public static function create () {
        // Validate the user input
        api_validate([
            'name' => Accounts::NAME_VALIDATION,
            'type' => Accounts::TYPE_VALIDATION,
            'user_id' => Accounts::USER_ID_ADMIN_VALIDATION,
            'amount' => Accounts::AMOUNT_VALIDATION
        ]);

        // Insert the account to the database
        Accounts::insert([
            'name' => request('name'),
            'type' => request('type'),
            'user_id' => request('user_id'),
            'amount' => parse_money_number(request('amount'))
        ]);

        // Return a confirmation message
        return [
            'message' => 'The account has been created successfully',
            'account_id' => Database::lastInsertId()
        ];
    }

    // The API admin accounts show route
    public static function show ($account) {
        return $account;
    }

    // The API admin accounts edit route
    public static function edit ($account) {
        // Validate the user input
        api_validate([
            'name' => Accounts::NAME_VALIDATION,
            'type' => Accounts::TYPE_VALIDATION,
            'user_id' => Accounts::USER_ID_ADMIN_VALIDATION,
            'amount' => Accounts::AMOUNT_VALIDATION,
        ]);

        // Update the account in the database
        Accounts::update($account->id, [
            'name' => request('name'),
            'type' => request('type'),
            'user_id' => request('user_id'),
            'amount' => parse_money_number(request('amount'))
        ]);

        // Return a confirmation message
        return [
            'message' => 'The account has been edited successfully'
        ];
    }

    // The API admin accounts delete route
    public static function delete ($account) {
        // Delete the account
        Accounts::delete($user->id);

        // Return a confirmation message
        return [
            'message' => 'The account has been deleted successfully'
        ];
    }
}
