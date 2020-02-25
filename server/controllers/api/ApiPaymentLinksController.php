<?php

class ApiPaymentLinksController {
    // The API payment links index route
    public static function index () {
        // The pagination vars
        $page = request('page', 1);
        $limit = (int)request('limit', 20);
        if ($limit < 0) $limit = 1;
        if ($limit > 50) $limit = 50;
        $count = PaymentLinks::count();

        // Select all the payment links by page
        $paymentLinks = PaymentLinks::selectPage($page, $limit)->fetchAll();

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'paymentLinks' => $paymentLinks
        ];
    }

    // The API payment links search route
    public static function search () {
        $q = request('q', '');

        // The pagination vars
        $page = request('page', 1);
        $limit = (int)request('limit', 20);
        if ($limit < 0) $limit = 1;
        if ($limit > 50) $limit = 50;
        $count = PaymentLinks::searchCount($q);

        // Select all the payment links by page
        $paymentLinks = PaymentLinks::searchPage($q, $page, $limit)->fetchAll();

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'paymentLinks' => $paymentLinks
        ];
    }

    // The API payment links create route
    public static function create () {
        return [
            'message' => 'Comming soon...'
        ];
    }

    // The API payment links show route
    public static function show ($transaction) {
        return $transaction;
    }
}
