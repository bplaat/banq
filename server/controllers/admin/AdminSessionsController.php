<?php

class AdminSessionsController {
    // The admin sessions index page
    public static function index () {
        // The pagination vars
        $page = get_page();
        $per_page = 9;
        $last_page = ceil(Sessions::count() / $per_page);

        // Fetch the sessions and there users
        $sessions = Sessions::selectPage($page, $per_page)->fetchAll();
        foreach ($sessions as $session) {
            $session->user = Users::select($session->user_id)->fetch();
        }

        // Give all data to the view
        return view('admin.sessions.index', [
            'sessions' => $sessions,
            'page' => $page,
            'last_page' => $last_page
        ]);
    }

    // The admin sessions show page
    public static function show ($session) {
        // Fetch the sessions user and give the data to the view
        $session->user = Users::select($session->user_id)->fetch();
        return view('admin.sessions.show', [ 'session' => $session ]);
    }

    // The admin sessions revoke route
    public static function revoke ($session) {
        // Revoke the session
        Auth::revokeSession($session->session);

        // Redirect to the sessions index page
        Router::redirect('/admin/sessions');
    }
}
