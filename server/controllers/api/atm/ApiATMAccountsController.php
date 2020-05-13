<?php

class ApiATMAccountsController {
    // The API ATM accounts show route
    public static function show ($account) {
        // Parse the account parts
        $account_parts = parseAccountParts($account);

        // Check if it is a banq account
        if (
            $account_parts['country'] != COUNTRY_CODE ||
            $account_parts['bank'] != BANK_CODE
        ) {
            return [
                'success' => false,
                'blocked' => false,
                'message' => 'This API supports only Banq cards'
            ];
        }

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

        $account = Accounts::select($account_parts['account'])->fetch();

        return [
            'success' => true,
            'blocked' => false,
            'account' => $account
        ];
    }
}
