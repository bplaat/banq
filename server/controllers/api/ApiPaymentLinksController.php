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
        // Validate the input vars
        api_validate([
            'name' => PaymentLinks::NAME_VALIDATION,
            'account_id' => PaymentLinks::ACCOUNT_ID_ADMIN_VALIDATION,
            'amount' => PaymentLinks::AMOUNT_VALIDATION
        ]);

        // Insert the payment link to the database
        PaymentLinks::insert([
            'name' => request('name'),
            'link' => PaymentLinks::generateLink(),
            'account_id' => request('account_id'),
            'amount' => parse_money_number(request('amount'))
        ]);

        // Return a confirmation message
        return [
            'message' => 'The payment link has been created successfully',
            'payment_link_id' => Database::lastInsertId()
        ];
    }

    // The API payment links show route
    public static function show ($transaction) {
        return $transaction;
    }
}
