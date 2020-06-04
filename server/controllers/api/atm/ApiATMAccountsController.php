<?php

class ApiATMAccountsController {
    // The API ATM accounts show route
    public static function show ($account) {
        // Parse the account parts
        $account_parts = parseAccountParts($account);

        // Check if it is a foreign bank account
        if (
            !(
                $account_parts['country'] == COUNTRY_CODE &&
                $account_parts['bank'] == BANK_CODE
            )
        ) {
            // Send to Gosbank
            $gosbank_response = json_decode(file_get_contents(GOSBANK_CLIENT_API_URL . '/gosbank/accounts/' . $account . '?pin=' . request('pincode')));

            // When success
            if ($gosbank_response->code == GOSBANK_CODE_SUCCESS) {
                return [
                    'success' => true,
                    'blocked' => false,
                    'account' => [
                        'id' => 0,
                        'name' => 'Gosbank Account',
                        'type' => Accounts::TYPE_PAYMENT,
                        'amount' => $gosbank_response->balance,
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                ];
            }

            // When pincode is false
            if ($gosbank_response->code == GOSBANK_CODE_AUTH_FAILED) {
                return [
                    'success' => false,
                    'blocked' => false,
                    'message' => 'Pincode false'
                ];
            }

            // When account is blocked
            if ($gosbank_response->code == GOSBANK_CODE_BLOCKED) {
                return [
                    'success' => false,
                    'blocked' => true,
                    'message' => 'This account is blocked'
                ];
            }

            // When account dont exists
            if ($gosbank_response->code == GOSBANK_CODE_DONT_EXISTS) {
                return [
                    'success' => false,
                    'blocked' => true,
                    'message' => 'This account don\'t exists'
                ];
            }

            // When broken message
            if ($gosbank_response->code == GOSBANK_CODE_BROKEN_MESSAGE) {
                return [
                    'success' => false,
                    'blocked' => true,
                    'message' => 'Broken message or other bank connection error'
                ];
            }
        }

        // Banq account
        else {
            // Get card if by account id
            $cardQuery = Cards::select([ 'account_id' => $account_parts['account'] ]);
            if ($cardQuery->rowCount() == 0) {
                return [
                    'success' => false,
                    'blocked' => true,
                    'message' => 'This account don\'t exists'
                ];
            }

            // Fetch card info
            $card = $cardQuery->fetch();

            // Check if card is blocked
            if ($card->blocked) {
                return [
                    'success' => false,
                    'blocked' => true,
                    'message' => 'This account is blocked'
                ];
            }

            // Check if the pincode matches
            if (!password_verify(request('pincode'), $card->pincode)) {
                $attempts = $card->attempts + 1;

                // Check if the attempts max
                if ($attempts == CARD_MAX_ATTEMPTS) {
                    Cards::update($card->id, [ 'blocked' => 1 ]);
                    return [
                        'success' => false,
                        'blocked' => true,
                        'message' => 'This account is now blocked'
                    ];
                }

                // If card is not blocked but pincode is false
                Cards::update($card->id, [ 'attempts' => $attempts ]);
                return [
                    'success' => false,
                    'blocked' => false,
                    'message' => 'Pincode false'
                ];
            }

            // The pincode is good reset attempts
            Cards::update($card->id, [ 'attempts' => 0 ]);

            // Fetch account info
            $account = Accounts::select($account_parts['account'])->fetch();

            // Return success message
            return [
                'success' => true,
                'blocked' => false,
                'account' => $account
            ];
        }
    }
}
