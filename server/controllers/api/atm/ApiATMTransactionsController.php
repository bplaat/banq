<?php

class ApiATMTransactionsController {
    // The API ATM transactions create route
    public static function create () {
        // Parse accounts
        $from_account_parts = parseAccountParts(request('from_account_id'));
        $to_account_parts = parseAccountParts(request('to_account_id'));

        // Check if it is a banq account
        if (
            $from_account_parts['country'] != COUNTRY_CODE ||
            $from_account_parts['bank'] != BANK_CODE
        ) {
            $gosbank_response = json_decode(file_get_contents(GOSBANK_CLIENT_API_URL + '/gosbank/transactions/create?from=' + request('from') + '&to=' + request('to') + '&pin=' + request('pincode') + '&amount=' + request('amount')));

            // When success
            if ($gosbank_response['code'] == GOSBANK_CODE_SUCCESS) {
                // Parse the amount
                $amount = parse_money_number(request('amount'));

                // Update account
                $to_account = Accounts::select(request('to_account_id'))->fetch();
                $to_account->amount += $amount;

                // Add the transaction to the database
                Transactions::insert([
                    'name' => request('name'),
                    'from_account_id' => request('from_account_id'),
                    'to_account_id' => $to_account_parts['account'],
                    'amount' => $amount
                ]);
                $transaction_id = Database::lastInsertId();

                // Update the account in the database
                Accounts::update($to_account_parts['account'], [ 'amount' => $to_account->amount ]);

                // Return a confirmation message
                return [
                    'success' => true,
                    'blocked' => false,
                    'transaction' => Transactions::select($transaction_id)->fetch()
                ];
            }

            // Not enough balance
            if ($gosbank_response['code'] == GOSBANK_CODE_NOT_ENOUGH_BALANCE) {
                return [
                    'success' => false,
                    'blocked' => false,
                    'message' => 'Not enough balance'
                ];
            }

            // Pincode false
            if ($gosbank_response['code'] == GOSBANK_CODE_AUTH_FAILED) {
                return [
                    'success' => false,
                    'blocked' => false,
                    'message' => 'Pincode false'
                ];
            }

            // When error account is blocked
            if (
                $gosbank_response['code'] == GOSBANK_CODE_BROKEN_MESSAGE ||
                $gosbank_response['code'] == GOSBANK_CODE_BLOCKED ||
                $gosbank_response['code'] == GOSBANK_CODE_DONT_EXISTS
            ) {
                return [
                    'success' => false,
                    'blocked' => true,
                    'message' => 'This account is blocked'
                ];
            }
        }

        $_REQUEST['from_account_id'] = $from_account_parts["account"];
        $_REQUEST['to_account_id'] = $to_account_parts["account"];

        // Validate the user input
        api_validate([
            'name' => Transactions::NAME_VALIDATION,
            'from_account_id' => Transactions::FROM_ACCOUNT_ID_ADMIN_VALIDATION,
            'rfid' => Cards::RFID_ADMIN_VALIDATION,
            'pincode' => Cards::PINCODE_VALIDATION,
            'to_account_id' => Transactions::TO_ACCOUNT_ID_VALIDATION,
            'amount' => Transactions::AMOUNT_VALIDATION
        ]);

        // Check if card is blocked
        $card = Cards::select([ 'rfid' => request('rfid') ])->fetch();
        if ($card->blocked) {
            return [
                'success' => false,
                'blocked' => true,
                'message' => 'This card is blocked'
            ];
        } else {
            // Check if the pincode matches
            if (!password_verify(request('pincode'), $card->pincode)) {
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
            } else {
                // The pincode is good reset attempts
                Cards::update($card->id, [ 'attempts' => 0 ]);
            }
        }

        // Parse the amount
        $amount = parse_money_number(request('amount'));

        // Update both accounts
        $from_account = Accounts::select(request('from_account_id'))->fetch();
        $from_account->amount -= $amount;
        $to_account = Accounts::select(request('to_account_id'))->fetch();
        $to_account->amount += $amount;

        // Add the transaction to the database
        Transactions::insert([
            'name' => request('name'),
            'from_account_id' => request('from_account_id'),
            'to_account_id' => request('to_account_id'),
            'amount' => $amount
        ]);
        $transaction_id = Database::lastInsertId();

        // Update the accounts in the database
        Accounts::update(request('from_account_id'), [ 'amount' => $from_account->amount ]);
        Accounts::update(request('to_account_id'), [ 'amount' => $to_account->amount ]);

        // Return a confirmation message
        return [
            'success' => true,
            'blocked' => false,
            'transaction' => Transactions::select($transaction_id)->fetch()
        ];
    }
}
