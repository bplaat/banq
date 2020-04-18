<?php

class ApiATMTransactionsController {
    // The API ATM transactions create route
    public static function create () {
        // Convert from account id string to banq id
        $from_account_id = request('from_account_id');

        if (substr($from_account_id, 0, strlen(BANK_CODE_PREFIX)) != BANK_CODE_PREFIX) {
            return [
                'success' => false,
                'blocked' => false,
                'message' => 'This API supports only Banq cards'
            ];
        }
        $_REQUEST['from_account_id'] = floatval(substr($from_account_id, strlen(BANK_CODE_PREFIX)));

        // Convert to account id string to banq id
        $to_account_id = request('to_account_id');
        if (substr($to_account_id, 0, strlen(BANK_CODE_PREFIX)) != BANK_CODE_PREFIX) {
            return [
                'success' => false,
                'blocked' => false,
                'message' => 'This API supports only Banq cards'
            ];
        }
        $_REQUEST['to_account_id'] = floatval(substr($to_account_id, strlen(BANK_CODE_PREFIX)));

        // Validate the user input
        api_validate([
            'name' => Transactions::NAME_VALIDATION,
            'from_account_id' => Transactions::FROM_ACCOUNT_ID_ADMIN_VALIDATION,
            'rfid' => Cards::RFID_VALIDATION,
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
