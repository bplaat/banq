<?php

// A function that gets the page number
function get_page () {
    return (int)request('page', 1);
}

// A function that gets the limit number
function get_limit () {
    $limit = (int)request('limit', 20);
    if ($limit < 0) $limit = 1;
    if ($limit > 50) $limit = 50;
    return $limit;
}

// A function that parse input money amount
function parse_money_number ($string) {
    return round(floatval($string) * 100);
}

// A function which formats money
function format_money_number ($money) {
    return number_format($money / 100, 2);
}

// A function which formats money with HTML
function format_money ($money) {
    return '<b>&euro; ' . format_money_number($money) . '</b>';
}

// A function which cuts a long string with dots
function cut ($string, $length) {
    return strlen($string) > $length ? substr($string, 0, $length) . '...' : $string;
}

// A function which dies and dump the data given to it
function dd ($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// A function which gives the user IP address
function get_ip () {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}
