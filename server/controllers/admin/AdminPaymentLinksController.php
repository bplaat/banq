<?php

class AdminPaymentLinksController {
    // The admin payment links index page
    public static function index () {
        // The pagination vars
        $page = request('page', 1);
        $per_page = 9;
        $last_page = ceil(PaymentLinks::count() / $per_page);

        // Select all the payment links and there accounts
        $paymentLinks = PaymentLinks::selectPage($page, $per_page)->fetchAll();
        foreach ($paymentLinks as $paymentLink) {
            $paymentLink->account = Accounts::select($paymentLink->account_id)->fetch();
        }

        // Give all the data to the view
        return view('admin.payment-links.index', [
            'paymentLinks' => $paymentLinks,
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

        // Generate a payment link link
        $link = PaymentLinks::generateLink();

        // Insert the payment link to the database
        PaymentLinks::insert([
            'name' => request('name'),
            'link' => $link,
            'account_id' => request('account_id'),
            'amount' => parse_money_number(request('amount'))
        ]);

        // Redirect to the new payment link show page
        Router::redirect('/admin/payment-links/' . $link);
    }

    // The payment links show page
    public static function show ($paymentLink) {
        $paymentLink->account = Accounts::select($paymentLink->account_id)->fetch();
        $paymentLink->absolute_link = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/pay/' . $paymentLink->link;
        return view('admin.payment-links.show', [ 'paymentLink' => $paymentLink ]);
    }

    // The payment links delete page
    public static function delete ($paymentLink) {
        PaymentLinks::delete($paymentLink->link);
        Router::redirect('/admin/payment-links');
    }
}
