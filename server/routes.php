<?php

Router::get('/', 'PagesController::index');
Router::get('/offline', 'PagesController::offline');

if (Auth::check()) {
    // Accounts
    Router::get('/accounts', 'AccountsController::index');
    Router::get('/accounts/create', 'AccountsController::create');
    Router::post('/accounts', 'AccountsController::store');
    Router::get('/accounts/{Accounts}/edit', 'AccountsController::edit');
    Router::post('/accounts/{Accounts}', 'AccountsController::update');
    Router::get('/accounts/{Accounts}/delete', 'AccountsController::delete');
    Router::get('/accounts/{Accounts}', 'AccountsController::show');

    // Transactions
    Router::get('/transactions', 'TransactionsController::index');
    Router::get('/transactions/create', 'TransactionsController::create');
    Router::post('/transactions', 'TransactionsController::store');
    Router::get('/transactions/{Transactions}', 'TransactionsController::show');

    // Settings
    Router::get('/auth/settings', 'SettingsController::showSettingsForm');
    Router::post('/auth/settings/change_details', 'SettingsController::changeDetails');
    Router::post('/auth/settings/change_password', 'SettingsController::changePassword');
    Router::get('/auth/settings/revoke_session/{Sessions}', 'SettingsController::revokeSession');
    Router::get('/auth/settings/delete', 'SettingsController::deleteUser');

    // Admin
    if (Auth::user()->role == Users::ROLE_ADMIN) {
        Router::get('/admin', 'AdminController::index');

        // Admin users
        Router::get('/admin/users', 'AdminUsersController::index');
        Router::get('/admin/users/create', 'AdminUsersController::create');
        Router::post('/admin/users', 'AdminUsersController::store');
        Router::get('/admin/users/{Users}/edit', 'AdminUsersController::edit');
        Router::post('/admin/users/{Users}', 'AdminUsersController::update');
        Router::get('/admin/users/{Users}/delete', 'AdminUsersController::delete');
        Router::get('/admin/users/{Users}', 'AdminUsersController::show');

        // Admin accounts
        Router::get('/admin/accounts', 'AdminAccountsController::index');
        Router::get('/admin/accounts/create', 'AdminAccountsController::create');
        Router::post('/admin/accounts', 'AdminAccountsController::store');
        Router::get('/admin/accounts/{Accounts}/edit', 'AdminAccountsController::edit');
        Router::post('/admin/accounts/{Accounts}', 'AdminAccountsController::update');
        Router::get('/admin/accounts/{Accounts}/delete', 'AdminAccountsController::delete');
        Router::get('/admin/accounts/{Accounts}', 'AdminAccountsController::show');

        // Admin transactions
        Router::get('/admin/transactions', 'AdminTransactionsController::index');
        Router::get('/admin/transactions/create', 'AdminTransactionsController::create');
        Router::post('/admin/transactions', 'AdminTransactionsController::store');
        Router::get('/admin/transactions/{Transactions}', 'AdminTransactionsController::show');
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

// 404 Not found
Router::fallback('PagesController::notFound');
