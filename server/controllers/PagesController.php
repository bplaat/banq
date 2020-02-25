<?php

class PagesController {
    // The home page
    public static function index () {
        return view('index');
    }

    // The offline page
    public static function offline () {
        return view('offline');
    }

    // The not found page
    public static function notfound () {
        http_response_code(404);
        return view('notfound');
    }
}
