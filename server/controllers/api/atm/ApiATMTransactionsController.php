<?php

class ApiATMTransactionsController {
    // The API ATM transactions create route
    public static function create () {
        // Convert from account id string to banq id
        $from_account_id = request('from_account_id');
        $banqCode = 'SU-BANQ-';
        if (substr($from_account_id, 0, strlen($banqCode)) != $banqCode) {
            return 'This API supports only Banq cards';
        }
        $_REQUEST['from_account_id'] = floatval(substr($from_account_id, strlen($banqCode)));

        // Convert to account id string to banq id
        $to_account_id = request('to_account_id');
        $banqCode = 'SU-BANQ-';
        if (substr($to_account_id, 0, strlen($banqCode)) != $banqCode) {
            return 'This API supports only Banq cards';
        }
        $_REQUEST['to_account_id'] = floatval(substr($to_account_id, strlen($banqCode)));

        // Validate the user input
        api_validate([
            'name' => Transactions::NAME_VALIDATION,
            'from_account_id' => Transactions::FROM_ACCOUNT_ID_VALIDATION,
            'rfid' => Cards::RFID_VALIDATION,
            'pincode' => Cards::PINCODE_VALIDATION,
            'to_account_id' => Transactions::TO_ACCOUNT_ID_VALIDATION,
            'amount' => Transactions::AMOUNT_VALIDATION
        ]);

        // Check if card is blocked
        $card = Cards::select([ 'rfid' => request('rfid') ])->fetch();
        if ($card->blocked) {
            return 'This card is blocked';
        } else {
            // Check if the pincode matches
            if (!password_verify(request('pincode'), $card->pincode)) {
                $attempts = $card->attempts + 1;

                // Check if the attempts max
                if ($attempts == CARD_MAX_ATTEMPTS) {
                    Cards::update($card->id, [ 'blocked' => 1 ]);
                    return 'Pincode ' . CARD_MAX_ATTEMPTS . ' times false, blocked the card';
                }

                // Increment the card attempts var
                else {
                    Cards::update($card->id, [ 'attempts' => $attempts ]);
                    return 'Pincode false';
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
            'message' => 'The transaction has been created successfully',
            'transaction_id' => $transaction_id
        ];
    }
}
