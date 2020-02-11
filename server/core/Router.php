<?php

class Router {
    public static function get ($route, $callback) {
        static::match(['get'], $route, $callback);
    }

    public static function post ($route, $callback) {
        static::match(['post'], $route, $callback);
    }

    public static function any ($route, $callback) {
        static::match(['get', 'post'], $route, $callback);
    }

    public static function match ($methods, $route, $callback) {
        $path = rtrim(preg_replace('#/+#', '/', strtok($_SERVER['REQUEST_URI'], '?')), '/');
        if ($path == '') $path = '/';

        if (
            in_array(strtolower($_SERVER['REQUEST_METHOD']), $methods) &&
            preg_match('#^' . preg_replace('/{.*}/U', '([^/]*)', $route) . '$#', $path, $values)
        ) {
            array_shift($values);
            preg_match('/{(.*)}/U', $route, $names);
            array_shift($names);
            foreach ($names as $index => $name) {
                if (class_exists($name)) {
                    $query = call_user_func($name . '::select', $values[$index]);
                    if ($query->rowCount() == 1) {
                        $values[$index] = $query->fetch();
                    } else {
                        return;
                    }
                }
            }

            $response = call_user_func_array($callback, $values);
            if (is_null($response)) {
                exit;
            }
            if (is_string($response)) {
                echo $response;
                exit;
            }
            if (is_array($response) || is_object($response)) {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
        }
    }

    public static function fallback ($callback) {
        $response = call_user_func($callback);
        if (is_null($response)) {
            exit;
        }
        if (is_string($response)) {
            echo $response;
            exit;
        }
        if (is_array($response) || is_object($response)) {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }

    public static function redirect ($route) {
        header('Location: ' . $route);
        exit;
    }

    public static function back () {
        static::redirect($_SERVER['HTTP_REFERER']);
    }
}
