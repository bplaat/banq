<?php

class AdminController {
    public static function index () {
        $users = Users::select()->fetchAll();
        return view('admin.index', [ 'users' => $users ]);
    }
}
