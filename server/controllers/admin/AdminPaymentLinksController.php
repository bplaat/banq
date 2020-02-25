<?php

class AdminPaymentLinksController {
    // The admin payment links index page
    public static function index () {
        // The pagination vars
        $page = get_page();
        $per_page = 9;

        // Check if search query is given
        if (request('q') != '') {
            $last_page = ceil(PaymentLinks::searchCount(request('q')) / $per_page);
            $payment_links = PaymentLinks::searchPage(request('q'), $page, $per_page)->fetchAll();
        } else {
            $last_page = ceil(PaymentLinks::count() / $per_page);
            $payment_links = PaymentLinks::selectPage($page, $per_page)->fetchAll();
        }

        // Select the account of every payment link
        foreach ($payment_links as $payment_link) {
            $payment_link->account = Accounts::select($payment_link->account_id)->fetch();
        }

        // Give all the data to the view
        return view('admin.payment-links.index', [
            'payment_links' => $payment_links,
            'page' => $page,
            'last_page' => $last_page
        ]);
    }

    // The admin payment links create page
    public static function create () {
        $accounts = Accounts::select([ 'type' => Accounts::TYPE_PAYMENT ])->fetchAll();
        return view('admin.payment-links.create', [
            'accounts' => $accounts,
            'account_id' => request('account_id')
        ]);
    }

    // The admin payment links store page
    public static function store () {
        // Validate the user input
        validate([
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

        // Redirect to the new payment link show page
        Router::redirect('/admin/payment-links/' . Database::lastInsertId());
    }

    // The payment links show page
    public static function show ($payment_link) {
        $payment_link->account = Accounts::select($payment_link->account_id)->fetch();
        $payment_link->absolute_link = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/pay/' . $payment_link->link;
        return view('admin.payment-links.show', [ 'payment_link' => $payment_link ]);
    }

    // The payment links delete page
    public static function delete ($payment_link) {
        PaymentLinks::delete($payment_link->id);
        Router::redirect('/admin/payment-links');
    }
}
