<?php

Router::get('/', 'PagesController::index');

if (Auth::check()) {
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
