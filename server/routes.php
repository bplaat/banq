<?php

Router::get('/', 'PagesController::index');

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
    Router::get('/auth/settings/delete', 'SettingsController::deleteAccount');

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
