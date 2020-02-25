<?php

class ApiAccountsController {
    // The API accounts index route
    public static function index () {
        // The pagination vars
        $page = request('page', 1);
        $limit = (int)request('limit', 20);
        if ($limit < 0) $limit = 1;
        if ($limit > 50) $limit = 50;
        $count = Accounts::count();

        // Select all the accounts by page
        $accounts = Accounts::selectPage($page, $limit)->fetchAll();
        foreach ($accounts as $account) {
            unset($account->key);
        }

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
        $page = request('page', 1);
        $limit = (int)request('limit', 20);
        if ($limit < 0) $limit = 1;
        if ($limit > 50) $limit = 50;
        $count = Accounts::searchCount($q);

        // Select all the accounts by page
        $accounts = Accounts::searchPage($q, $page, $limit)->fetchAll();
        foreach ($accounts as $account) {
            unset($account->key);
        }

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
        return [
            'message' => 'Comming soon...'
        ];
    }

    // The API accounts show route
    public static function show ($account) {
        unset($account->key);
        return $account;
    }

    // The API accounts edit route
    public static function edit ($account) {
        return [
            'message' => 'Comming soon...'
        ];
    }

    // The API accounts delete route
    public static function delete ($account) {
        return [
            'message' => 'Comming soon...'
        ];
    }
}
