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

function dd ($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function view ($_path, $_data = null) {
    if (!is_null($_data)) extract($_data);
    unset($_data);
    if (is_array(Session::get('messages'))) $messages = Session::get('messages');
    if (is_array(Session::get('errors'))) $errors = Session::get('errors');
    ob_start();
    eval('unset($_path) ?>' . preg_replace(
        ['/@view\((.*)\)/', '/@(.*)/', '/{{(.*)}}/U', '/{!!(.*)!!}/U'],
        ['<?php echo view($1) ?>', '<?php $1 ?>', '<?php echo htmlspecialchars($1, ENT_QUOTES, \'UTF-8\') ?>', '<?php echo $1 ?>'],
        file_get_contents(ROOT . '/views/' . str_replace('.', '/', $_path) . '.html')
    ));
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
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

require_once ROOT . '/core/parse_user_agent.php';
