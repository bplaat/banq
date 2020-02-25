<?php

class ApiUsersController {
    // The API users index route
    public static function index () {
        // The pagination vars
        $page = request('page', 1);
        $limit = (int)request('limit', 20);
        if ($limit < 0) $limit = 1;
        if ($limit > 50) $limit = 50;
        $count = Users::count();

        // Select all the users by page
        $users = Users::selectPage($page, $limit)->fetchAll();
        foreach ($users as $user) {
            unset($user->password);
        }

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'users' => $users
        ];
    }

    // The API users search route
    public static function search () {
        $q = request('q', '');

        // The pagination vars
        $page = request('page', 1);
        $limit = (int)request('limit', 20);
        if ($limit < 0) $limit = 1;
        if ($limit > 50) $limit = 50;
        $count = Users::searchCount($q);

        // Select all the users by page
        $users = Users::searchPage($q, $page, $limit)->fetchAll();
        foreach ($users as $user) {
            unset($user->password);
        }

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'users' => $users
        ];
    }

    // The API users create route
    public static function create () {
        return [
            'message' => 'Comming soon...'
        ];
    }

    // The API users show route
    public static function show ($user) {
        unset($user->password);
        return $user;
    }

    // The API users edit route
    public static function edit ($user) {
        return [
            'message' => 'Comming soon...'
        ];
    }

    // The API users delete route
    public static function delete ($user) {
        return [
            'message' => 'Comming soon...'
        ];
    }
}
