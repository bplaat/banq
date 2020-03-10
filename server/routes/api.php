<?php

// The API routes
Router::any('/api(.+)', function () {
    Auth::useCookie(false);
    header('Access-Control-Allow-Origin: *');

    // Check the api key
    if (APP_DEBUG || Devices::select([ 'key' => request('key') ])->rowCount() == 1) {
        // ATM accounts
        Router::any('/api/atm/accounts/{account_id}', 'ApiATMAccountsController::show');

        // ATM transactions
        Router::any('/api/atm/transactions/create', 'ApiATMTransactionsController::create');

        // API auth
        Router::any('/api/auth/login', 'ApiAuthController::login');
        Router::any('/api/auth/register', 'ApiAuthController::register');

        // Check the user session
        if (Auth::check()) {
            // API auth
            Router::any('/api/auth/logout', 'ApiAuthController::logout');
            Router::any('/api/auth/edit_details', 'ApiAuthController::editDetails');
            Router::any('/api/auth/edit_password', 'ApiAuthController::editPassword');
            Router::any('/api/auth/delete', 'ApiAuthController::delete');

            // API sessions
            Router::any('/api/sessions', 'ApiSessionsController::index');
            Router::any('/api/sessions/{Sessions}/revoke', 'ApiSessionsController::revoke');

            // API accounts
            Router::any('/api/accounts', 'ApiAccountsController::index');
            Router::any('/api/accounts/search', 'ApiAccountsController::search');
            Router::any('/api/accounts/create', 'ApiAccountsController::create');
            Router::any('/api/accounts/{Accounts}', 'ApiAccountsController::show');
            Router::any('/api/accounts/{Accounts}/edit', 'ApiAccountsController::edit');
            Router::any('/api/accounts/{Accounts}/delete', 'ApiAccountsController::delete');

            // API transactions
            Router::any('/api/transactions', 'ApiTransactionsController::index');
            Router::any('/api/transactions/search', 'ApiTransactionsController::search');
            Router::any('/api/transactions/create', 'ApiTransactionsController::create');
            Router::any('/api/transactions/{Transactions}', 'ApiTransactionsController::show');

            // API payment links
            Router::any('/api/payment-links', 'ApiPaymentLinksController::index');
            Router::any('/api/payment-links/search', 'ApiPaymentLinksController::search');
            Router::any('/api/payment-links/create', 'ApiPaymentLinksController::create');
            Router::any('/api/payment-links/{PaymentLinks}', 'ApiPaymentLinksController::show');
            Router::any('/api/payment-links/{PaymentLinks}/delete', 'ApiPaymentLinksController::delete');

            // API cards
            Router::any('/api/cards', 'ApiCardsController::index');
            Router::any('/api/cards/search', 'ApiCardsController::search');
            Router::any('/api/cards/create', 'ApiCardsController::create');
            Router::any('/api/cards/{Cards}', 'ApiCardsController::show');
            Router::any('/api/cards/{Cards}/delete', 'ApiCardsController::delete');

            // API Admin
            if (Auth::user()->role == Users::ROLE_ADMIN) {
                // API admin devices
                Router::any('/api/admin/devices', 'ApiAdminDevicesController::index');
                Router::any('/api/admin/devices/search', 'ApiAdminDevicesController::search');
                Router::any('/api/admin/devices/create', 'ApiAdminDevicesController::create');
                Router::any('/api/admin/devices/{Devices}', 'ApiAdminDevicesController::show');
                Router::any('/api/admin/devices/{Devices}/edit', 'ApiAdminDevicesController::edit');
                Router::any('/api/admin/devices/{Devices}/delete', 'ApiAdminDevicesController::delete');

                // API admin users
                Router::any('/api/admin/users', 'ApiAdminUsersController::index');
                Router::any('/api/admin/users/search', 'ApiAdminUsersController::search');
                Router::any('/api/admin/users/create', 'ApiAdminUsersController::create');
                Router::any('/api/admin/users/{Users}', 'ApiAdminUsersController::show');
                Router::any('/api/admin/users/{Users}/edit', 'ApiAdminUsersController::edit');
                Router::any('/api/admin/users/{Users}/delete', 'ApiAdminUsersController::delete');

                // API admin accounts
                Router::any('/api/admin/accounts', 'ApiAdminAccountsController::index');
                Router::any('/api/admin/accounts/search', 'ApiAdminAccountsController::search');
                Router::any('/api/admin/accounts/create', 'ApiAdminAccountsController::create');
                Router::any('/api/admin/accounts/{Accounts}', 'ApiAdminAccountsController::show');
                Router::any('/api/admin/accounts/{Accounts}/edit', 'ApiAdminAccountsController::edit');
                Router::any('/api/admin/accounts/{Accounts}/delete', 'ApiAdminAccountsController::delete');

                // API admin transactions
                Router::any('/api/admin/transactions', 'ApiAdminTransactionsController::index');
                Router::any('/api/admin/transactions/search', 'ApiAdminTransactionsController::search');
                Router::any('/api/admin/transactions/create', 'ApiAdminTransactionsController::create');
                Router::any('/api/admin/transactions/{Transactions}', 'ApiAdminTransactionsController::show');

                // API admin payment links
                Router::any('/api/admin/payment-links', 'ApiAdminPaymentLinksController::index');
                Router::any('/api/admin/payment-links/search', 'ApiAdminPaymentLinksController::search');
                Router::any('/api/admin/payment-links/create', 'ApiAdminPaymentLinksController::create');
                Router::any('/api/admin/payment-links/{PaymentLinks}', 'ApiAdminPaymentLinksController::show');
                Router::any('/api/admin/payment-links/{PaymentLinks}/delete', 'ApiAdminPaymentLinksController::delete');

                // API admin cards
                Router::any('/api/admin/cards', 'ApiAdminCardsController::index');
                Router::any('/api/admin/cards/search', 'ApiAdminCardsController::search');
                Router::any('/api/admin/cards/create', 'ApiAdminCardsController::create');
                Router::any('/api/admin/cards/{Cards}', 'ApiAdminCardsController::show');
                Router::any('/api/admin/cards/{Cards}/delete', 'ApiAdminCardsController::delete');

                // API admin sessions
                Router::any('/api/admin/sessions', 'ApiAdminSessionsController::index');
                Router::any('/api/admin/sessions/{Sessions}/revoke', 'ApiAdminSessionsController::revoke');
            }
        }

        // If invalid return standard message
        else {
            http_response_code(403);
            return [
                'message' => 'Invalid user session'
            ];
        }
    }

    // If invalid return standard message
    else {
        http_response_code(403);
        return [
            'message' => 'Invalid API key'
        ];
    }

    return false;
});
