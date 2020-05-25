<?php

class ApiAdminCardsController {
    // The API cards index route
    public static function index () {
        // The pagination vars
        $page = get_page();
        $limit = get_limit();
        $count = Cards::count();

        // Select all the cards by page
        $cards = Cards::selectPage($page, $limit)->fetchAll();
        foreach ($cards as $card) {
            unset($card->pincode);
        }

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'cards' => $cards
        ];
    }

    // The API cards search route
    public static function search () {
        $q = request('q', '');

        // The pagination vars
        $page = get_page();
        $limit = get_limit();
        $count = Cards::searchCount($q);

        // Select all the cards by page
        $cards = Cards::searchSelectPage($q, $page, $limit)->fetchAll();
        foreach ($cards as $card) {
            unset($card->pincode);
        }

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'cards' => $cards
        ];
    }

    // The API cards create route
    public static function create () {
        // Validate the input vars
        api_validate([
            'name' => Cards::NAME_VALIDATION,
            'account_id' => Cards::ACCOUNT_ID_ADMIN_VALIDATION,
            'rfid' => Cards::RFID_VALIDATION,
            'pincode' => Cards::PINCODE_VALIDATION
        ]);

        // Insert the card to the database
        Cards::insert([
            'name' => request('name'),
            'account_id' => request('account_id'),
            'rfid' => request('rfid'),
            'pincode' => password_hash(request('pincode'), PASSWORD_DEFAULT)
        ]);

        // Return a confirmation message
        return [
            'success' => true,
            'message' => 'The card has been created successfully',
            'card_id' => Database::lastInsertId()
        ];
    }

    // The API cards show route
    public static function show ($card) {
        unset($card->pincode);
        return $card;
    }

    // The API cards delete route
    public static function delete ($card) {
        // Delete the card
        Cards::delete($card->id);

        // Return a confirmation message
        return [
            'message' => 'The card has been deleted successfully'
        ];
    }
}
