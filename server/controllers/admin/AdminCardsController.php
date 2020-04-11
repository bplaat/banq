<?php

class AdminCardsController {
    // The admin cards index page
    public static function index () {
        // The pagination vars
        $page = get_page();
        $per_page = PAGINATION_LIMIT_ADMIN;

        // Check if search query is given
        if (request('q') != '') {
            $last_page = ceil(Cards::searchCount(request('q')) / $per_page);
            $cards = Cards::searchSelectPage(request('q'), $page, $per_page)->fetchAll();
        } else {
            $last_page = ceil(Cards::count() / $per_page);
            $cards = Cards::selectPage($page, $per_page)->fetchAll();
        }

        // Select the account of every card
        foreach ($cards as $card) {
            $card->account = Accounts::select($card->account_id)->fetch();
        }

        // Give all the data to the view
        return view('admin.cards.index', [
            'cards' => $cards,
            'page' => $page,
            'last_page' => $last_page
        ]);
    }

    // The admin cards create page
    public static function create () {
        $accounts = Accounts::select([ 'type' => Accounts::TYPE_PAYMENT ])->fetchAll();
        return view('admin.cards.create', [
            'accounts' => $accounts,
            'account_id' => request('account_id')
        ]);
    }

    // The admin card store page
    public static function store () {
        // Validate the user input
        validate([
            'name' => Cards::NAME_VALIDATION,
            'rfid' => Cards::RFID_VALIDATION,
            'account_id' => Cards::ACCOUNT_ID_ADMIN_VALIDATION,
            'pincode' => Cards::PINCODE_VALIDATION
        ]);

        // Insert the account to the database
        Cards::insert([
            'name' => request('name'),
            'rfid' => request('rfid'),
            'account_id' => request('account_id'),
            'pincode' => password_hash(request('pincode'), PASSWORD_DEFAULT)
        ]);

        // Redirect to the new accounts show page
        Router::redirect('/admin/cards/' . Database::lastInsertId());
    }

    // The admin card show page
    public static function show ($card) {
        $card->account = Accounts::select($card->account_id)->fetch();
        return view('admin.cards.show', [ 'card' => $card ]);
    }

    // The admin card block page
    public static function block ($card) {
        Cards::update($card->id, [
            'blocked' => 1
        ]);
        Router::redirect('/admin/cards/' . $card->id);
    }

    // The admin card unblock page
    public static function unblock ($card) {
        Cards::update($card->id, [
            'attempts' => 0,
            'blocked' => 0
        ]);
        Router::redirect('/admin/cards/' . $card->id);
    }

    // The admin card delete page
    public static function delete ($card) {
        Cards::delete($card->id);
        Router::redirect('/admin/cards');
    }
}
