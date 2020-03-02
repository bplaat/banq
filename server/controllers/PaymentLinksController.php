<?php

class PaymentLinksController {
    // The payment links index page
    public static function index () {
        // The pagination vars
        $page = get_page();
        $per_page = PAGINATION_LIMIT_NORMAL;

        // Check if search query is given
        if (request('q') != '') {
            $last_page = ceil(PaymentLinks::searchCountByUser(Auth::id(), request('q')) / $per_page);
            $payment_links = PaymentLinks::searchSelectPageByUser(Auth::id(), request('q'), $page, $per_page)->fetchAll();
        } else {
            $last_page = ceil(PaymentLinks::countByUser(Auth::id()) / $per_page);
            $payment_links = PaymentLinks::selectPageByUser(Auth::id(), $page, $per_page)->fetchAll();
        }

        // Select the account of every payment link
        foreach ($payment_links as $payment_link) {
            $payment_link->account = Accounts::select($payment_link->account_id)->fetch();
        }

        // Give all the data to the view
        return view('payment-links.index', [
            'payment_links' => $payment_links,
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
    public static function show ($payment_link) {
        // Check if the payment link is from authed user
        $payment_link->account = Accounts::select($payment_link->account_id)->fetch();
        if ($payment_link->account->user_id == Auth::id()) {
            $payment_link->absolute_link = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/pay/' . $payment_link->link;
            return view('payment-links.show', [ 'payment_link' => $payment_link ]);
        } else {
            // Else return 404 page
            return false;
        }
    }

    // The payment links delete page
    public static function delete ($payment_link) {
        // Check if the payment link is from authed user
        $payment_link->account = Accounts::select($payment_link->account_id)->fetch();
        if ($payment_link->account->user_id == Auth::id()) {
            PaymentLinks::delete($payment_link->id);
            Router::redirect('/payment-links');
        } else {
            // Else return 404 page
            return false;
        }
    }

    // The payment link pay page
    public static function pay ($link) {
        // Select the payment link by the link provided and select its account
        $payment_link = PaymentLinks::select([ 'link' => $link ])->fetch();
        $payment_link->account = Accounts::select($payment_link->account_id)->fetch();

        // Check if the user is authed then fetch his payment accounts
        if (Auth::check()) {
            $from_accounts = Accounts::select([ 'user_id' => Auth::id(), 'type' => Accounts::TYPE_PAYMENT ])->fetchAll();
            return view('payment-links.pay', [
                'payment_link' => $payment_link,
                'from_accounts' => $from_accounts,
                'from_account_id' => request('from_account_id')
            ]);
        } else {
            return view('payment-links.pay', [ 'payment_link' => $payment_link ]);
        }
    }

    // The payment link process payment page
    public static function processPayment ($link) {
        // Select the payment link by the link provided
        $payment_link = PaymentLinks::select([ 'link' => $link ])->fetch();

        // Validate the user input
        $_REQUEST['to_account_id'] = $payment_link->account_id;
        validate([
            'from_account_id' => Transactions::FROM_ACCOUNT_ID_VALIDATION,
        ]);

        // Update the effected accounts
        $from_account = Accounts::select(request('from_account_id'))->fetch();
        $from_account->amount -= $payment_link->amount;
        $to_account = Accounts::select($payment_link->account_id)->fetch();
        $to_account->amount += $payment_link->amount;

        // Create the transaction in the database
        Transactions::insert([
            'name' => $payment_link->name,
            'from_account_id' => request('from_account_id'),
            'to_account_id' => $payment_link->account_id,
            'amount' => $payment_link->amount
        ]);
        $transaction_id = Database::lastInsertId();

        // Update the accounts in the database
        Accounts::update(request('from_account_id'), [ 'amount' => $from_account->amount ]);
        Accounts::update($payment_link->account_id, [ 'amount' => $to_account->amount ]);

        // Redirect to the transactions page
        Router::redirect('/transactions/' . $transaction_id);
    }
}
