<?php

class AccountsController {
    // The accounts index page
    public static function index () {
        // Select all the accounts from the authed user and give them to the view
        $accounts = Accounts::select([ 'user_id' => Auth::id() ])->fetchAll();
        return view('accounts.index', [ 'accounts' => $accounts ]);
    }

    // The accounts create form page
    public static function create () {
        return view('accounts.create');
    }

    // The accounts store page
    public static function store () {
        // Validate the form fields and check max accounts limit
        $_REQUEST['user_id'] = Auth::id();
        validate([
            'name' => Accounts::NAME_VALIDATION,
            'type' => Accounts::TYPE_VALIDATION,
            'user_id' => Accounts::USER_ID_VALIDATION
        ]);

        // Insert the new account in the database
        Accounts::insert([
            'name' => request('name'),
            'type' => request('type'),
            'user_id' => Auth::id(),
            'amount' => 0
        ]);

        // Redirect to the new made account
        Router::redirect('/accounts/' . Database::lastInsertId());
    }

    // The account show page with paginated transcations
    public static function show ($account) {
        // Select information about the user of the account
        $account->user = Users::select($account->user_id)->fetch();

        // Pagination variables
        $page = get_page();
        $per_page = 5;
        $last_page = ceil(Transactions::countByAccount($account->id) / $per_page);

        // Select all transaction of the account by page and select the accounts info of each transaction
        $transactions = Transactions::selectPageByAccount($account->id, $page, $per_page)->fetchAll();
        foreach ($transactions as $transaction) {
            $transaction->from_account = Accounts::select($transaction->from_account_id)->fetch();
            $transaction->to_account = Accounts::select($transaction->to_account_id)->fetch();
        }

        // Give all the data to the view
        return view('accounts.show', [
            'account' => $account,
            'transactions' => $transactions,
            'page' => $page,
            'last_page' => $last_page
        ]);
    }

    // The account edit page
    public static function edit ($account) {
        // Check if the account is from the authed user
        if ($account->user_id == Auth::id()) {
            // Return the right view
            return view('accounts.edit', [ 'account' => $account ]);
        } else {
            // Return a 404 page
            return false;
        }
    }

    // The account update page
    public static function update ($account) {
        // Check if the account is from the authed user
        if ($account->user_id == Auth::id()) {
            // Valid the user input
            validate([
                'name' => Accounts::NAME_VALIDATION,
                'type' => Accounts::TYPE_VALIDATION
            ]);

            // Update the account with the new information
            Accounts::update($account->id, [
                'name' => request('name'),
                'type' => request('type')
            ]);

            // Redirect to the account show page
            Router::redirect('/accounts/' . $account->id);
        } else {
            // Return a 404 page
            return false;
        }
    }

    // The account delete page
    public static function delete ($account) {
        // Check if the account is from the authed user
        if ($account->user_id == Auth::id()) {
            // Delete the account from the database
            Accounts::delete($account->id);

            // Redirect to the accounts index page
            Router::redirect('/accounts');
        } else {
            // Return a 404 page
            return false;
        }
    }
}
