<?php

// The web routes

// Default pages
Router::get('/', 'PagesController::index');
Router::get('/offline', 'PagesController::offline');
Router::get('/api', 'PagesController::apiDocs');

// Payment links
Router::get('/pay/{link}', 'PaymentLinksController::pay');

// Check if the user is authed
if (Auth::check()) {
    // Accounts
    Router::get('/accounts', 'AccountsController::index');
    Router::get('/accounts/create', 'AccountsController::create');
    Router::post('/accounts', 'AccountsController::store');
    Router::get('/accounts/{Accounts}', 'AccountsController::show');
    Router::get('/accounts/{Accounts}/edit', 'AccountsController::edit');
    Router::post('/accounts/{Accounts}', 'AccountsController::update');
    Router::get('/accounts/{Accounts}/delete', 'AccountsController::delete');

    // Cards
    Router::get('/cards', 'CardsController::index');
    Router::get('/cards/{Cards}', 'CardsController::show');
    Router::get('/cards/{Cards}/block', 'CardsController::block');
    Router::get('/cards/{Cards}/delete', 'CardsController::delete');

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
    Router::get('/auth/sessions/{Sessions}/revoke', 'SettingsController::revokeSession');
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

        // Admin sessions
        Router::get('/admin/sessions', 'AdminSessionsController::index');
        Router::get('/admin/sessions/{Sessions}', 'AdminSessionsController::show');
        Router::get('/admin/sessions/{Sessions}/revoke', 'AdminSessionsController::revoke');

        // Admin accounts
        Router::get('/admin/accounts', 'AdminAccountsController::index');
        Router::get('/admin/accounts/create', 'AdminAccountsController::create');
        Router::post('/admin/accounts', 'AdminAccountsController::store');
        Router::get('/admin/accounts/{Accounts}', 'AdminAccountsController::show');
        Router::get('/admin/accounts/{Accounts}/edit', 'AdminAccountsController::edit');
        Router::post('/admin/accounts/{Accounts}', 'AdminAccountsController::update');
        Router::get('/admin/accounts/{Accounts}/delete', 'AdminAccountsController::delete');

        // Admin Cards
        Router::get('/admin/cards', 'AdminCardsController::index');
        Router::get('/admin/cards/create', 'AdminCardsController::create');
        Router::post('/admin/cards', 'AdminCardsController::store');
        Router::get('/admin/cards/{Cards}', 'AdminCardsController::show');
        Router::get('/admin/cards/{Cards}/block', 'AdminCardsController::block');
        Router::get('/admin/cards/{Cards}/unblock', 'AdminCardsController::unblock');
        Router::get('/admin/cards/{Cards}/delete', 'AdminCardsController::delete');

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

// The non authed user pages
else {
    // Auth
    Router::get('/auth/login', 'AuthController::showLoginForm');
    Router::post('/auth/login', 'AuthController::login');
    Router::get('/auth/register', 'AuthController::showRegisterForm');
    Router::post('/auth/register', 'AuthController::register');
}

// 404 Not found
Router::fallback('PagesController::notFound');
