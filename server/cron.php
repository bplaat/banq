<?php

// The Banq cron job tasks
// This file must be run by a cron job every hour

// Check if the framework is allready loaded
if (!defined('ROOT')) {
    // Set the right autoloaders
    define('ROOT', dirname(__FILE__));

    spl_autoload_register(function ($class) {
        $file = ROOT . '/core/' . $class . '.php';
        if (file_exists($file)) require_once $file;
    });

    spl_autoload_register(function ($class) {
        $file = ROOT . '/models/' . $class . '.php';
        if (file_exists($file)) require_once $file;
    });

    // Load the config file
    require_once ROOT . '/config.php';

    // Connect to the datbase
    Database::connect(DATABASE_DSN, DATABASE_USER, DATABASE_PASSWORD);
}

// Pay interest to all save accounts task
function pay_interest () {
    // Select all savings accounts and loop trough every one
    $save_accounts = Accounts::select([ 'type' => Accounts::TYPE_SAVE ])->fetchAll();
    foreach ($save_accounts as $account) {
        // Calculate the right amount of interest
        $amount = round($account->amount * (INTEREST_RATE / 100), 2);

        // Add a new interest transaction to the database
        Transactions::insert([
            'name' => 'Interest at ' . date('Y-m-d H:i:s'),
            'from_account_id' => ADMIN_INTEREST_ACCOUNT_ID,
            'to_account_id' => $account->id,
            'amount' => $amount
        ]);

        // Update the amount of the account in the database
        Accounts::update($account->id, [
            'amount' => $account->amount + $amount
        ]);
    }
}

// Run all tasks
pay_interest();
