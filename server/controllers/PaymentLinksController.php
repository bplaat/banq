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
        $amount = parse_money_number(request('amount'));

        PaymentLinks::insert([
            'name' => request('name'),
            'link' => $link,
            'account_id' => request('account_id'),
            'amount' => $amount
        ]);

        Router::redirect('/payment-links/' . $link);
    }

    public static function show ($paymentLink) {
        $paymentLink->account = Accounts::select($paymentLink->account_id)->fetch();
        $paymentLink->absolute_link = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/pay/' . $paymentLink->link;
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
        $paymentLink->account = Accounts::select($paymentLink->account_id)->fetch();
        if (Auth::check()) {
            $from_accounts = Accounts::select([ 'user_id' => Auth::id() ])->fetchAll();
            return view('payment-links.pay', [
                'paymentLink' => $paymentLink,
                'from_accounts' => $from_accounts,
                'from_account_id' => request('from_account_id')
            ]);
        } else {
            return view('payment-links.pay', [ 'paymentLink' => $paymentLink ]);
        }
    }

    public static function processPayment ($paymentLink) {
        $_REQUEST['to_account_id'] = $paymentLink->account_id;
        validate([
            'from_account_id' => Transactions::FROM_ACCOUNT_ID_VALIDATION,
            'from_account_id' => 'Accounts::RIGHT_OWNER_VALIDATION',
            'from_account_id' => 'Accounts::ENOUGH_AMOUNT_VALIDATION'
        ]);

        $from_account = Accounts::select(request('from_account_id'))->fetch();
        $from_account->amount -= $paymentLink->amount;
        $to_account = Accounts::select($paymentLink->account_id)->fetch();
        $to_account->amount += $paymentLink->amount;

        Transactions::insert([
            'name' => $paymentLink->name,
            'from_account_id' => request('from_account_id'),
            'to_account_id' => $paymentLink->account_id,
            'amount' => $paymentLink->amount
        ]);
        $transaction_id = Database::lastInsertId();

        Accounts::update(request('from_account_id'), [ 'amount' => $from_account->amount ]);
        Accounts::update($paymentLink->account_id, [ 'amount' => $to_account->amount ]);

        Router::redirect('/transactions/' . $transaction_id);
    }
}
