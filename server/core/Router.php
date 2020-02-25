<?php

// The frameworks router
class Router {
    // A wrapper function to match a get route
    public static function get ($route, $callback) {
        static::match(['get'], $route, $callback);
    }

    // A wrapper function to match a post route
    public static function post ($route, $callback) {
        static::match(['post'], $route, $callback);
    }

    // A wrapper function to match any route
    public static function any ($route, $callback) {
        static::match(['get', 'post'], $route, $callback);
    }

    // A function which handles the response return by the callback
    public static function handleResponse ($response) {
        // Stop running when nothing is returned
        if (is_null($response)) {
            exit;
        }

        // Echo and stop running when a string is returned
        if (is_string($response)) {
            echo $response;
            exit;
        }

        // Echo the json and stop when an array or object is returned
        if (is_array($response) || is_object($response)) {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }

    // A function which matches a route with the current route
    public static function match ($methods, $route, $callback) {
        // Remove trailing slashes from the request path
        $path = rtrim(preg_replace('#/+#', '/', strtok($_SERVER['REQUEST_URI'], '?')), '/');
        if ($path == '') $path = '/';

        // Check if the route is the same as the url
        if (
            in_array(strtolower($_SERVER['REQUEST_METHOD']), $methods) &&
            preg_match('#^' . preg_replace('/{.*}/U', '([^/]*)', $route) . '$#', $path, $values)
        ) {
            array_shift($values);

            // Do some route model binding
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

            // Call the callback
            static::handleResponse(call_user_func_array($callback, $values));
        }
    }

    // A function whichs is a fallback when all other routes didn't match can be used for a 404 page
    public static function fallback ($callback) {
        static::handleResponse(call_user_func($callback));
    }

    // A function that redirects to a specific route
    public static function redirect ($route) {
        header('Location: ' . $route);
        exit;
    }

    // A function that redirects page to the previous page
    public static function back () {
        static::redirect($_SERVER['HTTP_REFERER']);
    }
}
