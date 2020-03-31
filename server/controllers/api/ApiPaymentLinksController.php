<?php

class ApiPaymentLinksController {
    // The API payment links index route
    public static function index () {
        // The pagination vars
        $page = get_page();
        $limit = get_limit();
        $count = PaymentLinks::countByUser(Auth::id());

        // Select all the payment links by page
        $payment_links = PaymentLinks::selectPageByUser(Auth::id(), $page, $limit)->fetchAll();

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'payment_links' => $payment_links
        ];
    }

    // The API payment links search route
    public static function search () {
        $q = request('q', '');

        // The pagination vars
        $page = get_page();
        $limit = get_limit();
        $count = PaymentLinks::searchCountByUser(Auth::id(), $q);

        // Select all the payment links by page
        $payment_links = PaymentLinks::searchSelectPageByUser(Auth::id(), $q, $page, $limit)->fetchAll();

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'payment_links' => $payment_links
        ];
    }

    // The API payment links create route
    public static function create () {
        // Validate the input vars
        api_validate([
            'name' => PaymentLinks::NAME_VALIDATION,
            'to_account_id' => PaymentLinks::TO_ACCOUNT_ID_VALIDATION,
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

    // The API payment links show route
    public static function show ($payment_link) {
        // Check if the account is from the authed user
        $account = Accounts::select($payment_link->to_account_id)->fetch();
        if ($account->user_id == Auth::id()) {
            return $payment_link;
        }

        // Return a error message
        else {
            return [
                'message' => 'The payment link is not yours'
            ];
        }
    }

    // The API payment links delete route
    public static function delete ($payment_link) {
        // Check if the account is from the authed user
        $account = Accounts::select($payment_link->to_account_id)->fetch();
        if ($account->user_id == Auth::id()) {
            // Delete the payment link
            PaymentLinks::delete($payment_link->id);

            // Return a confirmation message
            return [
                'message' => 'The payment link has been deleted successfully'
            ];
        }

        // Return a error message
        else {
            return [
                'message' => 'The payment link is not yours'
            ];
        }
    }
}
