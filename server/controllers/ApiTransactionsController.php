<?php

class ApiTransactionsController {
    // The API transactions index route
    public static function index () {
        // The pagination vars
        $page = request('page', 1);
        $limit = (int)request('limit', 20);
        if ($limit < 0) $limit = 1;
        if ($limit > 50) $limit = 50;
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
        $page = request('page', 1);
        $limit = (int)request('limit', 20);
        if ($limit < 0) $limit = 1;
        if ($limit > 50) $limit = 50;
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
        return [
            'message' => 'Comming soon...'
        ];
    }

    // The API transactions show route
    public static function show ($transaction) {
        return $transaction;
    }
}
