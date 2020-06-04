<?php

class ApiATMTransactionsController {
    // The API ATM transactions create route
    public static function create () {
        // Parse accounts parts
        $from_account_parts = parseAccountParts(request('from_account_id'));
        $to_account_parts = parseAccountParts(request('to_account_id'));

        // Parse amount
        $amount = parse_money_number(request('amount'));

        // Check if amount is not 0 or smaller
        if ($amount <= 0) {
            return [
                'success' => false,
                'blocked' => false,
                'message' => 'Amount can\'t be negative'
            ];
        }

        // Check we pay money foreign bank account
        if (
            (
                $from_account_parts['country'] == COUNTRY_CODE &&
                $from_account_parts['bank'] == BANK_CODE
            ) &&
            !(
                $to_account_parts['country'] == COUNTRY_CODE &&
                $to_account_parts['bank'] == BANK_CODE
            )
        ) {
            // Get card if by account id
            $cardQuery = Cards::select([ 'account_id' => $from_account_parts['account'] ]);
            if ($cardQuery->rowCount() == 0) {
                return [
                    'success' => false,
                    'blocked' => true,
                    'message' => 'This card don\'t exist'
                ];
            }

            // Fetch card info
            $card = $cardQuery->fetch();

            // Check if card is blocked
            if ($card->blocked) {
                return [
                    'success' => false,
                    'blocked' => true,
                    'message' => 'This card is blocked'
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
                        'message' => 'This card is now blocked'
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
            $from_account = Accounts::select($from_account_parts['account'])->fetch();

            // Check saldo
            if ($from_account->amount < $amount) {
                return [
                    'success' => false,
                    'blocked' => false,
                    'message' => 'Not enough balance'
                ];
            }

            // Send to Gosbank
            $gosbank_response = json_decode(file_get_contents(GOSBANK_CLIENT_API_URL . '/gosbank/transactions/create?from=' . request('from_account_id') . '&to=' . request('to_account_id') . '&pin=' . request('pincode') . '&amount=' . request('amount')));

            // When success
            if ($gosbank_response->code == GOSBANK_CODE_SUCCESS) {
                // Add the transaction to the database
                Transactions::insert([
                    'name' => request('name'),
                    'from_account_id' => $from_account_parts['account'],
                    'to_account_id' => request('to_account_id'),
                    'amount' => $amount
                ]);
                $transaction_id = Database::lastInsertId();

                // Update the accounts in the database
                Accounts::update($from_account_parts['account'], [
                    'amount' => $from_account->amount - $amount
                ]);

                // Convert ids to account string
                $transaction = Transactions::select($transaction_id)->fetch();
                $transaction->from_account_id = formatAccountString($transaction->from_account_id);

                // Return a confirmation message
                return [
                    'success' => true,
                    'blocked' => false,
                    'transaction' => $transaction
                ];
            }

            // On errror
            else {
                return [
                    'success' => false,
                    'blocked' => true,
                    'message' => 'Other side didn\'t accepted the transaction, error code: ' . $gosbank_response->code
                ];
            }
        }

        // Check we get money from foreign bank account
        else if (
            !(
                $from_account_parts['country'] == COUNTRY_CODE &&
                $from_account_parts['bank'] == BANK_CODE
            ) &&
            (
                $to_account_parts['country'] == COUNTRY_CODE &&
                $to_account_parts['bank'] == BANK_CODE
            )
        ) {
            // Send to Gosbank
            $gosbank_response = json_decode(file_get_contents(GOSBANK_CLIENT_API_URL . '/gosbank/transactions/create?from=' . request('from_account_id') . '&to=' . request('to_account_id') . '&pin=' . request('pincode') . '&amount=' . request('amount')));

            // When success
            if ($gosbank_response->code == GOSBANK_CODE_SUCCESS) {
                // Fetch to account info
                $to_account = Accounts::select($to_account_parts['account'])->fetch();

                // Add the transaction to the database
                Transactions::insert([
                    'name' => request('name'),
                    'from_account_id' => request('from_account_id'),
                    'to_account_id' => $to_account_parts['account'],
                    'amount' => $amount
                ]);
                $transaction_id = Database::lastInsertId();

                // Update the account in the database
                Accounts::update($to_account_parts['account'], [
                    'amount' => $to_account->amount + $amount
                ]);

                // Convert ids to account string
                $transaction = Transactions::select($transaction_id)->fetch();
                $transaction->to_account_id = formatAccountString($transaction->to_account_id);

                // Return a confirmation message
                return [
                    'success' => true,
                    'blocked' => false,
                    'transaction' => $transaction
                ];
            }

            // Not enough balance
            if ($gosbank_response->code == GOSBANK_CODE_NOT_ENOUGH_BALANCE) {
                return [
                    'success' => false,
                    'blocked' => false,
                    'message' => 'Not enough balance'
                ];
            }

            // Pincode false
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

        // Else In house transaction
        else {
            // Get card if by account id
            $cardQuery = Cards::select([ 'account_id' => $from_account_parts['account'] ]);
            if ($cardQuery->rowCount() == 0) {
                return [
                    'success' => false,
                    'blocked' => true,
                    'message' => 'This card don\'t exist'
                ];
            }

            // Fetch card info
            $card = $cardQuery->fetch();

            // Check if card is blocked
            if ($card->blocked) {
                return [
                    'success' => false,
                    'blocked' => true,
                    'message' => 'This card is blocked'
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
                        'message' => 'This card is now blocked'
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
            $from_account = Accounts::select($from_account_parts['account'])->fetch();
            $to_account = Accounts::select($to_account_parts['account'])->fetch();

            // Check saldo
            if ($from_account->amount < $amount) {
                return [
                    'success' => false,
                    'blocked' => false,
                    'message' => 'Not enough balance'
                ];
            }

            // Add the transaction to the database
            Transactions::insert([
                'name' => request('name'),
                'from_account_id' => $from_account_parts['account'],
                'to_account_id' => $to_account_parts['account'],
                'amount' => $amount
            ]);
            $transaction_id = Database::lastInsertId();

            // Update the accounts in the database
            Accounts::update($from_account_parts['account'], [
                'amount' => $from_account->amount - $amount
            ]);
            Accounts::update($to_account_parts['account'], [
                'amount' => $to_account->amount + $amount
            ]);

            // Convert ids to account string
            $transaction = Transactions::select($transaction_id)->fetch();
            $transaction->from_account_id = formatAccountString($transaction->from_account_id);
            $transaction->to_account_id = formatAccountString($transaction->to_account_id);

            // Return a confirmation message
            return [
                'success' => true,
                'blocked' => false,
                'transaction' => $transaction
            ];
        }
    }
}
