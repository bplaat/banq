<?php

class ApiAdminSessionsController {
    // The API admin sessions index route
    public static function index () {
        // The pagination vars
        $page = get_page();
        $limit = get_limit();
        $count = Sessions::count();

        // Select all the sessions by page
        $sessions = Sessions::selectPage($page, $limit)->fetchAll();

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'sessions' => $sessions
        ];
    }

    // The API admin sessions revoke route
    public static function revoke ($session) {
        // Revoke the session
        Auth::revokeSession($session->session);

        // Return a confirmation message
        return [
            'message' => 'The session has been revoked successfully'
        ];
    }
}
