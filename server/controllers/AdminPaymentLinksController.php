<?php

class AdminPaymentLinksController {
    public static function index () {
        $page = request('page', 1);
        $per_page = 9;
        $last_page = ceil(PaymentLinks::count() / $per_page);
        $paymentLinks = PaymentLinks::selectPage($page, $per_page)->fetchAll();
        foreach ($paymentLinks as $paymentLink) {
            $paymentLink->account = Accounts::select($paymentLink->account_id)->fetch();
        }
        return view('admin.payment-links.index', [ 'paymentLinks' => $paymentLinks, 'page' => $page, 'last_page' => $last_page ]);
    }

    public static function create () {
        $accounts = Accounts::select()->fetchAll();
        return view('admin.payment-links.create', [
            'accounts' => $accounts,
            'account_id' => request('account_id')
        ]);
    }

    public static function store () {
        validate([
            'name' => PaymentLinks::NAME_VALIDATION,
            'account_id' => PaymentLinks::ACCOUNT_ID_VALIDATION,
            'amount' => PaymentLinks::AMOUNT_VALIDATION
        ]);

        $link = PaymentLinks::generateLink();

        PaymentLinks::insert([
            'name' => request('name'),
            'link' => $link,
            'account_id' => request('account_id'),
            'amount' => request('amount')
        ]);

        Router::redirect('/admin/payment-links/' . $link);
    }

    public static function show ($paymentLink) {
        $paymentLink->account = Accounts::select($paymentLink->account_id)->fetch();
        $paymentLink->absolute_link = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/pay/' . $paymentLink->link;
        return view('admin.payment-links.show', [ 'paymentLink' => $paymentLink ]);
    }

    public static function delete ($paymentLink) {
        PaymentLinks::delete($paymentLink->link);
        Router::redirect('/admin/payment-links');
    }
}
