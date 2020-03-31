<?php

class ApiAdminPaymentLinksController {
    // The API admin payment links index route
    public static function index () {
        // The pagination vars
        $page = get_page();
        $limit = get_limit();
        $count = PaymentLinks::count();

        // Select all the payment links by page
        $payment_links = PaymentLinks::selectPage($page, $limit)->fetchAll();

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'payment_links' => $payment_links
        ];
    }

    // The API admin payment links search route
    public static function search () {
        $q = request('q', '');

        // The pagination vars
        $page = get_page();
        $limit = get_limit();
        $count = PaymentLinks::searchCount($q);

        // Select all the payment links by page
        $payment_links = PaymentLinks::searchSelectPage($q, $page, $limit)->fetchAll();

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'payment_links' => $payment_links
        ];
    }

    // The API admin payment links create route
    public static function create () {
        // Validate the input vars
        api_validate([
            'name' => PaymentLinks::NAME_VALIDATION,
            'to_account_id' => PaymentLinks::TO_ACCOUNT_ID_ADMIN_VALIDATION,
            'amount' => PaymentLinks::AMOUNT_VALIDATION
        ]);

        // Insert the payment link to the database
        PaymentLinks::insert([
            'name' => request('name'),
            'link' => PaymentLinks::generateLink(),
            'to_account_id' => request('to_account_id'),
            'amount' => parse_money_number(request('amount'))
        ]);

        // Return a confirmation message
        return [
            'message' => 'The payment link has been created successfully',
            'payment_link_id' => Database::lastInsertId()
        ];
    }

    // The API admin payment links show route
    public static function show ($payment_link) {
        return $payment_link;
    }

    // The API admin payment links delete route
    public static function delete ($payment_link) {
        // Delete the payment link
        PaymentLinks::delete($payment_link->id);

        // Return a confirmation message
        return [
            'message' => 'The payment link has been deleted successfully'
        ];
    }
}
