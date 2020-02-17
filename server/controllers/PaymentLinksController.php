<?php

class PaymentLinksController {
    public static function index () {
        $page = request('page', 1);
        $per_page = 5;
        $last_page = ceil(PaymentLinks::countAllByUser() / $per_page);
        $paymentLinks = PaymentLinks::selectAllByUser($page, $per_page)->fetchAll();
        foreach ($paymentLinks as $paymentLink) {
            $paymentLink->account = Accounts::select($paymentLink->account_id)->fetch();
        }
        return view('payment-links.index', [ 'paymentLinks' => $paymentLinks, 'page' => $page, 'last_page' => $last_page ]);
    }

    public static function create () {
        $accounts = Accounts::select([ 'user_id' => Auth::id() ])->fetchAll();
        return view('payment-links.create', [
            'accounts' => $accounts,
            'account_id' => request('account_id')
        ]);
    }

    public static function store () {
        validate([
            'name' => PaymentLinks::NAME_VALIDATION,
            'account_id' => PaymentLinks::ACCOUNT_ID_VALIDATION,
            'amount' => PaymentLinks::AMOUNT_VALIDATION,
            'account_id' => 'Accounts::RIGHT_OWNER_VALIDATION'
        ]);

        $link = PaymentLinks::generateLink();

        PaymentLinks::insert([
            'name' => request('name'),
            'link' => $link,
            'account_id' => request('account_id'),
            'amount' => request('amount')
        ]);

        Router::redirect('/payment-links/' . $link);
    }

    public static function show ($paymentLink) {
        $paymentLink->account = Accounts::select($paymentLink->account_id)->fetch();
        if ($paymentLink->account->user_id == Auth::id()) {
            return view('payment-links.show', [ 'paymentLink' => $paymentLink ]);
        } else {
            return false;
        }
    }

    public static function delete ($paymentLink) {
        $paymentLink->account = Accounts::select($paymentLink->account_id)->fetch();
        if ($paymentLink->account->user_id == Auth::id()) {
            PaymentLinks::delete($paymentLink->link);
            Router::redirect('/payment-links');
        } else {
            return false;
        }
    }

    public static function pay ($paymentLink) {
        return 'Comming soon...';
    }

    public static function payNoAuth ($paymentLink) {
        return 'Comming soon...';
    }
}
