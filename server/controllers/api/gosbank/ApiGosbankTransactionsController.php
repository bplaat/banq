<?php

class ApiGosbankTransactionsController {
    // The API gosbank transactions create route
    public static function create () {
        // Parse the account parts
        $from_account_parts = parseAccountParts(request('from'));
        $to_account_parts = parseAccountParts(request('to'));

        // Parse amount
        $amount = parse_money_number(request('amount'));

        // Check if amount is not 0 or smaller
        if ($amount <= 0) {
            return [
                'code' => GOSBANK_CODE_BROKEN_MESSAGE
            ];
        }

        // Check if we pay money
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
            $account = Accounts::select($from_account_parts['account'])->fetch();

            // Check saldo
            if ($account->amount < $amount) {
                return [
                    'code' => GOSBANK_CODE_NOT_ENOUGH_BALANCE
                ];
            }

            // Create new transaction
            Transactions::insert([
                'name' => 'Gosbank Transaction on ' . date('Y-m-d H:i:s'),
                'from_account_id' => $account->id,
                'to_account_id' => request('to'),
                'amount' => $amount
            ]);

            // Update account saldo
            Accounts::update($account->id, [
                'amount' => $account->amount - $amount
            ]);

            // Return success message
            return [
                'code' => GOSBANK_CODE_SUCCESS
            ];
        }

        // Check if money is comming to us
        if (
            !(
                $from_account_parts['country'] == COUNTRY_CODE &&
                $from_account_parts['bank'] == BANK_CODE
            ) &&
            (
                $to_account_parts['country'] == COUNTRY_CODE &&
                $to_account_parts['bank'] == BANK_CODE
            )
        ) {
            // Fetch account info
            $account = Accounts::select($to_account_parts['account'])->fetch();

            // Create new transaction
            Transactions::insert([
                'name' => 'To Gosbank Transaction ' . date('Y-m-d H:i:s'),
                'from_account_id' => request('from'),
                'to_account_id' => $account->id,
                'amount' => $amount
            ]);

            // Update account saldo
            Accounts::update($account->id, [
                'amount' => $account->amount + $amount
            ]);

            // Return success message
            return [
                'code' => GOSBANK_CODE_SUCCESS
            ];
        }

        // When Banq is not involed or when message it self send broken message
        return [
            'code' => GOSBANK_CODE_BROKEN_MESSAGE
        ];
    }
}
