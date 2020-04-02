<?php

class ApiATMAccountsController {
    // The API ATM actions show route
    public static function show ($account_id) {
        // Convert account id string to banq id
        if (substr($account_id, 0, strlen(BANK_CODE_PREFIX)) != BANK_CODE_PREFIX) {
            return [
                'success' => false,
                'blocked' => false,
                'message' => 'This API supports only Banq cards'
            ];
        }
        $account_id = floatval(substr($account_id, strlen(BANK_CODE_PREFIX)));

        // Validate the user input
        api_validate([
            'rfid' => Cards::RFID_VALIDATION,
            'pincode' => Cards::PINCODE_VALIDATION
        ]);

        // Check if card is blocked
        $cardQuery = Cards::select([ 'rfid' => request('rfid') ]);
        if ($cardQuery->rowCount() == 0) {
            return [
                'success' => false,
                'blocked' => false,
                'message' => 'This card is not found in the Banq database'
            ];
        }

        $card = $cardQuery->fetch();
        if ($card->blocked) {
            return [
                'success' => false,
                'blocked' => true,
                'message' => 'This card is blocked'
            ];
        } else {
            // Check if the pincode matches
            if (password_verify(request('pincode'), $card->pincode)) {
                // The pincode is good reset attempts
                Cards::update($card->id, [ 'attempts' => 0 ]);
            } else {
                $attempts = $card->attempts + 1;

                // Check if the attempts max
                if ($attempts == CARD_MAX_ATTEMPTS) {
                    Cards::update($card->id, [ 'blocked' => 1 ]);
                    return [
                        'success' => false,
                        'blocked' => true,
                        'message' => 'This card is now blocked'
                    ];
                }

                // Increment the card attempts var
                else {
                    Cards::update($card->id, [ 'attempts' => $attempts ]);
                    return [
                        'success' => false,
                        'blocked' => false,
                        'message' => 'Pincode false'
                    ];
                }
            }
        }

        $account = Accounts::select($account_id)->fetch();

        return [
            'success' => true,
            'blocked' => false,
            'account' => $account
        ];
    }
}
