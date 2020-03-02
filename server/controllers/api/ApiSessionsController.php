<?php

class ApiSessionsController {
    // The API sessions index route
    public static function index () {
        // The pagination vars
        $page = get_page();
        $limit = get_limit();
        $count = Sessions::countByUser(Auth::id());

        // Select all the sessions by page
        $sessions = Sessions::selectPageByUser(Auth::id(), $page, $limit)->fetchAll();

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'sessions' => $sessions
        ];
    }

    // The API sessions revoke route
    public static function revoke ($session) {
        // Check if the session is from the authed user
        if ($session->user_id == Auth::id()) {
            // Revoke the session
            Auth::revokeSession($session->session);

            // Return a confirmation message
            return [
                'message' => 'The session has been revoked successfully'
            ];
        } else {
            // Return a error message
            http_response_code(403);
            return [
                'message' => 'The session is not yours'
            ];
        }
    }
}
