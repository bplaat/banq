<?php

class AdminAccountsController {
    // The admin accounts index page
    public static function index () {
        // The pagination vars
        $page = get_page();
        $per_page = PAGINATION_LIMIT_ADMIN;

        // Check if search query is given
        if (request('q') != '') {
            $last_page = ceil(Accounts::searchCount(request('q')) / $per_page);
            $accounts = Accounts::searchSelectPage(request('q'), $page, $per_page)->fetchAll();
        } else {
            $last_page = ceil(Accounts::count() / $per_page);
            $accounts = Accounts::selectPage($page, $per_page)->fetchAll();
        }

        // Give the data to the view
        return view('admin.accounts.index', [
            'accounts' => $accounts,
            'page' => $page,
            'last_page' => $last_page
        ]);
    }

    // The admin accounts create page
    public static function create () {
        $users = Users::select()->fetchAll();
        return view('admin.accounts.create', [
            'users' => $users,
            'user_id' => request('user_id')
        ]);
    }

    // The admin accounts store page
    public static function store () {
        // Validate the user input
        validate([
            'name' => Accounts::NAME_VALIDATION,
            'type' => Accounts::TYPE_VALIDATION,
            'user_id' => Accounts::USER_ID_ADMIN_VALIDATION,
            'amount' => Accounts::AMOUNT_VALIDATION
        ]);

        // Insert the account to the database
        Accounts::insert([
            'name' => request('name'),
            'type' => request('type'),
            'user_id' => request('user_id'),
            'amount' => parse_money_number(request('amount'))
        ]);

        // Redirect to the new accounts show page
        Router::redirect('/admin/accounts/' . Database::lastInsertId());
    }

    // The admin accounts show page
    public static function show ($account) {
        // Select the accounts user information
        $account->user = Users::select($account->user_id)->fetch();

        // The pagination vars
        $page = get_page();
        $per_page = PAGINATION_LIMIT_NORMAL;
        $last_page = ceil(Transactions::countByAccount($account->id) / $per_page);

        // Select all the transactions of the account and there accounts
        $transactions = Transactions::selectPageByAccount($account->id, $page, $per_page)->fetchAll();
        foreach ($transactions as $transaction) {
            $transaction->from_account = Accounts::select($transaction->from_account_id)->fetch();
            $transaction->to_account = Accounts::select($transaction->to_account_id)->fetch();
        }

        // Give all the data to the view
        return view('admin.accounts.show', [
            'account' => $account,
            'transactions' => $transactions,
            'page' => $page,
            'last_page' => $page
        ]);
    }

    // The admin accounts edit page
    public static function edit ($account) {
        $users = Users::select()->fetchAll();
        return view('admin.accounts.edit', [
            'account' => $account,
            'users' => $users
        ]);
    }

    // The admin accounts update page
    public static function update ($account) {
        // Validate the user input
        validate([
            'name' => Accounts::NAME_VALIDATION,
            'type' => Accounts::TYPE_VALIDATION,
            'user_id' => Accounts::USER_ID_ADMIN_VALIDATION,
            'amount' => Accounts::AMOUNT_VALIDATION,
        ]);

        // Update the account in the database
        Accounts::update($account->id, [
            'name' => request('name'),
            'type' => request('type'),
            'user_id' => request('user_id'),
            'amount' => parse_money_number(request('amount'))
        ]);

        // Redirect to the account page
        Router::redirect('/admin/accounts/' . $account->id);
    }

    // The admin accounts delete page
    public static function delete ($account) {
        Accounts::delete($account->id);
        Router::redirect('/admin/accounts');
    }
}
