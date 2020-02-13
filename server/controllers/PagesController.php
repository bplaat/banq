<?php

class PagesController {
    public static function index () {
        return view('index');
    }

    public static function offline () {
        return view('offline');
    }

    public static function notfound () {
        http_response_code(404);
        return view('notfound');
    }
}
