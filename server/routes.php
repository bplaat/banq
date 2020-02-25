<?php

// Default pages
Router::get('/', 'PagesController::index');
Router::get('/offline', 'PagesController::offline');

// Payment links
Router::get('/pay/{link}', 'PaymentLinksController::pay');

if (Auth::check()) {
    // Accounts
    Router::get('/accounts', 'AccountsController::index');
    Router::get('/accounts/create', 'AccountsController::create');
    Router::post('/accounts', 'AccountsController::store');
    Router::get('/accounts/{Accounts}', 'AccountsController::show');
    Router::get('/accounts/{Accounts}/edit', 'AccountsController::edit');
    Router::post('/accounts/{Accounts}', 'AccountsController::update');
    Router::get('/accounts/{Accounts}/delete', 'AccountsController::delete');

    // Transactions
    Router::get('/transactions', 'TransactionsController::index');
    Router::get('/transactions/create', 'TransactionsController::create');
    Router::post('/transactions', 'TransactionsController::store');
    Router::get('/transactions/{Transactions}', 'TransactionsController::show');

    // Payment links
    Router::get('/payment-links', 'PaymentLinksController::index');
    Router::get('/payment-links/create', 'PaymentLinksController::create');
    Router::post('/payment-links', 'PaymentLinksController::store');
    Router::get('/payment-links/{PaymentLinks}', 'PaymentLinksController::show');
    Router::get('/payment-links/{PaymentLinks}/delete', 'PaymentLinksController::delete');
    Router::post('/pay/{link}', 'PaymentLinksController::processPayment');

    // Settings
    Router::get('/auth/settings', 'SettingsController::showSettingsForm');
    Router::post('/auth/settings/change_details', 'SettingsController::changeDetails');
    Router::post('/auth/settings/change_password', 'SettingsController::changePassword');
    Router::get('/auth/settings/revoke_session/{Sessions}', 'SettingsController::revokeSession');
    Router::get('/auth/settings/delete', 'SettingsController::deleteUser');

    // Admin
    if (Auth::user()->role == Users::ROLE_ADMIN) {
        Router::get('/admin', 'AdminController::index');

        // Admin devices
        Router::get('/admin/devices', 'AdminDevicesController::index');
        Router::get('/admin/devices/create', 'AdminDevicesController::create');
        Router::post('/admin/devices', 'AdminDevicesController::store');
        Router::get('/admin/devices/{Devices}', 'AdminDevicesController::show');
        Router::get('/admin/devices/{Devices}/edit', 'AdminDevicesController::edit');
        Router::post('/admin/devices/{Devices}', 'AdminDevicesController::update');
        Router::get('/admin/devices/{Devices}/delete', 'AdminDevicesController::delete');

        // Admin users
        Router::get('/admin/users', 'AdminUsersController::index');
        Router::get('/admin/users/create', 'AdminUsersController::create');
        Router::post('/admin/users', 'AdminUsersController::store');
        Router::get('/admin/users/{Users}', 'AdminUsersController::show');
        Router::get('/admin/users/{Users}/edit', 'AdminUsersController::edit');
        Router::post('/admin/users/{Users}', 'AdminUsersController::update');
        Router::get('/admin/users/{Users}/delete', 'AdminUsersController::delete');

        // Admin accounts
        Router::get('/admin/accounts', 'AdminAccountsController::index');
        Router::get('/admin/accounts/create', 'AdminAccountsController::create');
        Router::post('/admin/accounts', 'AdminAccountsController::store');
        Router::get('/admin/accounts/{Accounts}', 'AdminAccountsController::show');
        Router::get('/admin/accounts/{Accounts}/edit', 'AdminAccountsController::edit');
        Router::post('/admin/accounts/{Accounts}', 'AdminAccountsController::update');
        Router::get('/admin/accounts/{Accounts}/delete', 'AdminAccountsController::delete');

        // Admin transactions
        Router::get('/admin/transactions', 'AdminTransactionsController::index');
        Router::get('/admin/transactions/create', 'AdminTransactionsController::create');
        Router::post('/admin/transactions', 'AdminTransactionsController::store');
        Router::get('/admin/transactions/{Transactions}', 'AdminTransactionsController::show');

        // Admin payment links
        Router::get('/admin/payment-links', 'AdminPaymentLinksController::index');
        Router::get('/admin/payment-links/create', 'AdminPaymentLinksController::create');
        Router::post('/admin/payment-links', 'AdminPaymentLinksController::store');
        Router::get('/admin/payment-links/{PaymentLinks}', 'AdminPaymentLinksController::show');
        Router::get('/admin/payment-links/{PaymentLinks}/delete', 'AdminPaymentLinksController::delete');
    }

    // Auth
    Router::get('/auth/logout', 'AuthController::logout');
}

else {
    // Auth
    Router::get('/auth/login', 'AuthController::showLoginForm');
    Router::post('/auth/login', 'AuthController::login');
    Router::get('/auth/register', 'AuthController::showRegisterForm');
    Router::post('/auth/register', 'AuthController::register');
}

// API
Router::any('/api(.*)', function () {
    header('Access-Control-Allow-Origin: *');

    // Check the api key
    if (APP_DEBUG || Devices::select([ 'key' => request('key') ])->rowCount() == 1) {
        // API devices
        Router::any('/api/devices', 'ApiDevicesController::index');
        Router::any('/api/devices/search', 'ApiDevicesController::search');
        Router::any('/api/devices/create', 'ApiDevicesController::create');
        Router::any('/api/devices/{Devices}', 'ApiDevicesController::show');
        Router::any('/api/devices/{Devices}/edit', 'ApiDevicesController::edit');
        Router::any('/api/devices/{Devices}/delete', 'ApiDevicesController::delete');

        // API users
        Router::any('/api/users', 'ApiUsersController::index');
        Router::any('/api/users/search', 'ApiUsersController::search');
        Router::any('/api/users/create', 'ApiUsersController::create');
        Router::any('/api/users/{Users}', 'ApiUsersController::show');
        Router::any('/api/users/{Users}/edit', 'ApiUsersController::edit');
        Router::any('/api/users/{Users}/delete', 'ApiUsersController::delete');

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
    }

    // If invalid return standard message
    else {
        http_response_code(403);
        return [
            'message' => 'Wrong API key'
        ];
    }

    return false;
});

// 404 Not found
Router::fallback('PagesController::notFound');
