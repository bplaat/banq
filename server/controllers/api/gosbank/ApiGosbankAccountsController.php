<?php

class ApiGosbankAccountsController {
    // The API gosbank accounts show route
    public static function show ($account) {
        // Parse the account parts
        $account_parts = parseAccountParts($account);

        // Check if it is a banq account
        if (
            $account_parts['country'] != COUNTRY_CODE ||
            $account_parts['bank'] != BANK_CODE
        ) {
            return [
                'code' => GOSBANK_CODE_BROKEN_MESSAGE
            ];
        }

        // Get card if by account id
        $cardQuery = Cards::select([ 'account_id' => $account_parts['account'] ]);
        if ($cardQuery->rowCount() == 0) {
            return [
                'code' => GOSBANK_CODE_DONT_EXISTS
            ];
        }

        // Fetch card info
        $card = $cardQuery->fetch();

        // Check if card is blocked
        if ($card->blocked) {
            return [
                'code' => GOSBANK_CODE_BLOCKED
            ];
        }

        // Check if the pincode matches
        if (!password_verify(request('pincode'), $card->pincode)) {
            $attempts = $card->attempts + 1;

            // Check if the attempts max
            if ($attempts == CARD_MAX_ATTEMPTS) {
                Cards::update($card->id, [ 'blocked' => 1 ]);
                return [
                    'code' => GOSBANK_CODE_BLOCKED
                ];
            }

            // If card is not blocked but pincode is false
            Cards::update($card->id, [ 'attempts' => $attempts ]);
            return [
                'code' => GOSBANK_CODE_AUTH_FAILED,
                'attempts' => $attempts
            ];
        }

        // The pincode is good reset attempts
        Cards::update($card->id, [ 'attempts' => 0 ]);

        // Fetch account info
        $account = Accounts::select($account_parts['account'])->fetch();

        // Return success message
        return [
            'code' => 200,
            'balance' => (float)$account->amount
        ];
    }
}
