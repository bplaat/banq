<?php

if (ROOT == null) {
    define('ROOT', dirname(__FILE__));

    spl_autoload_register(function ($class) {
        $file = ROOT . '/core/' . $class . '.php';
        if (file_exists($file)) require_once $file;
    });

    spl_autoload_register(function ($class) {
        $file = ROOT . '/models/' . $class . '.php';
        if (file_exists($file)) require_once $file;
    });

    require_once ROOT . '/config.php';

    Database::connect(DATABASE_DSN, DATABASE_USER, DATABASE_PASSWORD);
}

// Pay interest to all save accounts
$save_accounts = Accounts::select([ 'type' => Accounts::TYPE_SAVE ])->fetchAll();
foreach ($save_accounts as $account) {
    $amount = ceil($account->amount * (INTEREST_RATE / 100));
    Transactions::insert([
        'name' => 'Interest at ' . date('Y-m-d H:i:s'),
        'from_account_id' => 1,
        'to_account_id' => $account->id,
        'amount' => $amount
    ]);
    Accounts::update($account->id, [ 'amount' => $account->amount + $amount ]);
}
