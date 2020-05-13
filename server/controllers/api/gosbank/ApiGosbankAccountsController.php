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

        // TODO

        return [
            'code' => 200
        ];
    }
}
