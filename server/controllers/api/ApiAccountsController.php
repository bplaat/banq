<?php

class ApiAccountsController {
    // The API accounts index route
    public static function index () {
        // The pagination vars
        $page = get_page();
        $limit = get_limit();
        $count = Accounts::countByUser(Auth::id());

        // Select all the accounts by page
        $accounts = Accounts::selectPageByUser(Auth::id(), $page, $limit)->fetchAll();

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'accounts' => $accounts
        ];
    }

    // The API accounts search route
    public static function search () {
        $q = request('q', '');

        // The pagination vars
        $page = get_page();
        $limit = get_limit();
        $count = Accounts::searchCountByUser(Auth::id(), $q);

        // Select all the accounts by page
        $accounts = Accounts::searchSelectPageByUser(Auth::id(), $q, $page, $limit)->fetchAll();

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'accounts' => $accounts
        ];
    }

    // The API accounts create route
    public static function create () {
        // Validate the user input
        api_validate([
            'name' => Accounts::NAME_VALIDATION,
            'type' => Accounts::TYPE_VALIDATION
        ]);

        // Insert the account to the database
        Accounts::insert([
            'name' => request('name'),
            'type' => request('type'),
            'user_id' => Auth::id(),
            'amount' => 0
        ]);

        // Return a confirmation message
        return [
            'message' => 'The account has been created successfully',
            'account_id' => Database::lastInsertId()
        ];
    }

    // The API accounts show route
    public static function show ($account) {
        // Check if the account is from the authed user
        if ($account->user_id == Auth::id()) {
            return $account;
        }

        // Return a error message
        else {
            return [
                'message' => 'The account is not yours'
            ];
        }
    }

    // The API accounts edit route
    public static function edit ($account) {
        // Check if the account is from the authed user
        if ($account->user_id == Auth::id()) {
            // Validate the user input
            api_validate([
                'name' => Accounts::NAME_VALIDATION,
                'type' => Accounts::TYPE_VALIDATION
            ]);

            // Update the account in the database
            Accounts::update($account->id, [
                'name' => request('name'),
                'type' => request('type')
            ]);

            // Return a confirmation message
            return [
                'message' => 'The account has been edited successfully'
            ];
        }

        // Return a error message
        else {
            return [
                'message' => 'The account is not yours'
            ];
        }
    }

    // The API accounts delete route
    public static function delete ($account) {
        // Check if the account is from the authed user
        if ($account->user_id == Auth::id()) {
            // Delete the account
            Accounts::delete($user->id);

            // Return a confirmation message
            return [
                'message' => 'The account has been deleted successfully'
            ];
        }

        // Return a error message
        else {
            return [
                'message' => 'The account is not yours'
            ];
        }
    }
}
