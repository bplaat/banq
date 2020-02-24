<?php

function request ($key, $default = '') {
    return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
}

foreach ($_REQUEST as $key => $value) {
    Session::flash('old_' . $key, $value);
}

function old ($key, $default = '') {
    return Session::get('old_' . $key, $default);
}

function validate ($values) {
    $errors = Session::get('errors', []);
    foreach ($values as $key => $value) {
        $string = request($key);
        $rules = explode('|', $value);
        foreach ($rules as $rule) {
            if (substr($rule, 0, 1) == '@') {
                $error = call_user_func(substr($rule, 1), $key, $string);
                if (is_string($error)) {
                    $errors[] = $error;
                }
            } else {
                $parts = explode(':', $rule);
                $args = isset($parts[1]) ? explode(',', $parts[1]) : [];
                if ($parts[0] == 'required' && $string == '') {
                    $errors[] = 'The ' . $key . ' field is required';
                }
                if ($parts[0] == 'int' && !is_numeric($string) && $string != round($string)) {
                    $errors[] = 'The ' . $key . ' field must be a rounded number';
                }
                if ($parts[0] == 'float' && !is_numeric($string)) {
                    $errors[] = 'The ' . $key . ' field must be a number';
                }
                if ($parts[0] == 'email' && !filter_var($string, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'The ' . $key . ' field must be an email address';
                }
                if ($parts[0] == 'url' && !filter_var($string, FILTER_VALIDATE_URL)) {
                    $errors[] = 'The ' . $key . ' field must be an url';
                }
                if ($parts[0] == 'date' && strtotime($string) == false) {
                    $errors[] = 'The ' . $key . ' field must be an date';
                }
                if ($parts[0] == 'number_min' && $string < $args[0]) {
                    $errors[] = 'The ' . $key . ' field must be at least ' . $args[0] . ' or higher';
                }
                if ($parts[0] == 'number_max' && $string > $args[0]) {
                    $errors[] = 'The ' . $key . ' field must be a maximum of ' . $args[0] . ' or lower';
                }
                if ($parts[0] == 'number_between' && $string < $args[0] && $string > $args[1]) {
                    $errors[] = 'The ' . $key . ' field must be between ' . $args[0] . ' and ' . $args[1];
                }
                if ($parts[0] == 'confirmed' && $string != request($key . '_confirmation')) {
                    $errors[] = 'The ' . $key . ' fields must be the same';
                }
                if ($parts[0] == 'min' && strlen($string) < $args[0]) {
                    $errors[] = 'The ' . $key . ' field must be at least ' . $args[0] . ' characters long';
                }
                if ($parts[0] == 'max' && strlen($string) > $args[0]) {
                    $errors[] = 'The ' . $key . ' field can be a maximum of ' . $args[0] . ' characters';
                }
                if ($parts[0] == 'size' && strlen($string) != $args[0]) {
                    $errors[] = 'The ' . $key . ' field must be ' . $args[0] . ' characters long';
                }
                if ($parts[0] == 'same' && $string != request($args[0])) {
                    $errors[] = 'The ' . $key . ' field must be the same as the ' . $args[0] . ' field';
                }
                if ($parts[0] == 'different' && $string == request($args[0])) {
                    $errors[] = 'The ' . $key . ' field must be different as the ' . $args[0] . ' field';
                }
                if ($parts[0] == 'exists' && call_user_func($args[0] . '::select', [ (isset($args[1]) ? $args[1] : $key) => $string ])->rowCount() != 1) {
                    $errors[] = 'The ' . $key . ' field must refer to something that exists';
                }
                if ($parts[0] == 'unique' && call_user_func($args[0] . '::select', [ (isset($args[1]) ? $args[1] : $key) => $string ])->rowCount() != 0) {
                    $errors[] = 'The ' . $key . ' field must be unqiue';
                }
            }
        }
    }
    if (count($errors) > 0) {
        Session::flash('errors', $errors);
        Router::back();
    }
}
