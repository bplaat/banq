<?php

class CardsController {
    // The cards index page
    public static function index () {
        // The pagination vars
        $page = get_page();
        $per_page = PAGINATION_LIMIT_NORMAL;

        // Check if search query is given
        if (request('q') != '') {
            $last_page = ceil(Cards::searchCountByUser(Auth::id(), request('q')) / $per_page);
            $cards = Cards::searchSelectPageByUser(Auth::id(), request('q'), $page, $per_page)->fetchAll();
        } else {
            $last_page = ceil(Cards::countByUser(Auth::id()) / $per_page);
            $cards = Cards::selectPageByUser(Auth::id(), $page, $per_page)->fetchAll();
        }

        // Select the account of every card
        foreach ($cards as $card) {
            $card->account = Accounts::select($card->account_id)->fetch();
        }

        // Give all the data to the view
        return view('cards.index', [
            'cards' => $cards,
            'page' => $page,
            'last_page' => $last_page
        ]);
    }

    // The card show page
    public static function show ($card) {
        // Check if the card is from authed user
        $card->account = Accounts::select($card->account_id)->fetch();
        if ($card->account->user_id == Auth::id()) {
            return view('cards.show', [ 'card' => $card ]);
        } else {
            // Else return 404 page
            return false;
        }
    }

    // The card delete page
    public static function delete ($card) {
        // Check if the card is from authed user
        $card->account = Accounts::select($card->account_id)->fetch();
        if ($card->account->user_id == Auth::id()) {
            Cards::delete($card->id);
            Router::redirect('/cards');
        } else {
            // Else return 404 page
            return false;
        }
    }
}
