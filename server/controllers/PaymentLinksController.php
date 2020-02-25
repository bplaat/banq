<?php

class PaymentLinksController {
    // The payment links index page
    public static function index () {
        // The pagination vars
        $page = request('page', 1);
        $per_page = 5;
        $last_page = ceil(PaymentLinks::countAllByUser() / $per_page);

        // Select all the payment links by user and there accounts
        $paymentLinks = PaymentLinks::selectAllByUser($page, $per_page)->fetchAll();
        foreach ($paymentLinks as $paymentLink) {
            $paymentLink->account = Accounts::select($paymentLink->account_id)->fetch();
        }

        // Give all the data to the view
        return view('payment-links.index', [
            'paymentLinks' => $paymentLinks,
            'page' => $page,
            'last_page' => $last_page
        ]);
    }

    // The payment links create page
    public static function create () {
        $accounts = Accounts::select([ 'user_id' => Auth::id(), 'type' => Accounts::TYPE_PAYMENT ])->fetchAll();
        return view('payment-links.create', [
            'accounts' => $accounts,
            'account_id' => request('account_id')
        ]);
    }

    // The payment links store page
    public static function store () {
        // Validate the users input fields
        validate([
            'name' => PaymentLinks::NAME_VALIDATION,
            'account_id' => PaymentLinks::ACCOUNT_ID_VALIDATION,
            'amount' => PaymentLinks::AMOUNT_VALIDATION
        ]);

        // Insert the new payment link to the database
        PaymentLinks::insert([
            'name' => request('name'),
            'link' => PaymentLinks::generateLink(),
            'account_id' => request('account_id'),
            'amount' => parse_money_number(request('amount'))
        ]);

        // Redirect to the new payment link show page
        Router::redirect('/payment-links/' . Database::lastInsertId());
    }

    // The payment links show page
    public static function show ($paymentLink) {
        // Check if the payment link is from authed user
        $paymentLink->account = Accounts::select($paymentLink->account_id)->fetch();
        if ($paymentLink->account->user_id == Auth::id()) {
            $paymentLink->absolute_link = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/pay/' . $paymentLink->link;
            return view('payment-links.show', [ 'paymentLink' => $paymentLink ]);
        } else {
            // Else return 404 page
            return false;
        }
    }

    // The payment links delete page
    public static function delete ($paymentLink) {
        // Check if the payment link is from authed user
        $paymentLink->account = Accounts::select($paymentLink->account_id)->fetch();
        if ($paymentLink->account->user_id == Auth::id()) {
            PaymentLinks::delete($paymentLink->id);
            Router::redirect('/payment-links');
        } else {
            // Else return 404 page
            return false;
        }
    }

    // The payment link pay page
    public static function pay ($link) {
        // Select the payment link by the link provided and select its account
        $paymentLink = PaymentLinks::select([ 'link' => $link ])->fetch();
        $paymentLink->account = Accounts::select($paymentLink->account_id)->fetch();

        // Check if the user is authed then fetch his payment accounts
        if (Auth::check()) {
            $from_accounts = Accounts::select([ 'user_id' => Auth::id(), 'type' => Accounts::TYPE_PAYMENT ])->fetchAll();
            return view('payment-links.pay', [
                'paymentLink' => $paymentLink,
                'from_accounts' => $from_accounts,
                'from_account_id' => request('from_account_id')
            ]);
        } else {
            return view('payment-links.pay', [ 'paymentLink' => $paymentLink ]);
        }
    }

    // The payment link process payment page
    public static function processPayment ($link) {
        // Select the payment link by the link provided
        $paymentLink = PaymentLinks::select([ 'link' => $link ])->fetch();

        // Validate the user input
        $_REQUEST['to_account_id'] = $paymentLink->account_id;
        validate([
            'from_account_id' => Transactions::FROM_ACCOUNT_ID_VALIDATION,
        ]);

        // Update the effected accounts
        $from_account = Accounts::select(request('from_account_id'))->fetch();
        $from_account->amount -= $paymentLink->amount;
        $to_account = Accounts::select($paymentLink->account_id)->fetch();
        $to_account->amount += $paymentLink->amount;

        // Create the transaction in the database
        Transactions::insert([
            'name' => $paymentLink->name,
            'from_account_id' => request('from_account_id'),
            'to_account_id' => $paymentLink->account_id,
            'amount' => $paymentLink->amount
        ]);
        $transaction_id = Database::lastInsertId();

        // Update the accounts in the database
        Accounts::update(request('from_account_id'), [ 'amount' => $from_account->amount ]);
        Accounts::update($paymentLink->account_id, [ 'amount' => $to_account->amount ]);

        // Redirect to the transactions page
        Router::redirect('/transactions/' . $transaction_id);
    }
}
