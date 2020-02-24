<?php

function parse_money_number ($string) {
    return round(floatval($string) * 100);
}

function format_money_number ($money) {
    return number_format($money / 100, 2);
}

function format_money ($money) {
    return '<b>&euro; ' . format_money_number($money) . '</b>';
}

function cut ($string, $length) {
    return strlen($string) > $length ? substr($string, 0, $length) . '...' : $string;
}

function dd ($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function get_ip () {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}
