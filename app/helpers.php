<?php

if (! function_exists('linkify_mentions')) {
    /**
     * Convert @username mentions into profile links when the user exists.
     * Safe to call from views and controllers.
     *
     * @param  string  $html
     * @param  string  $linkClass
     * @return string
     */
    function linkify_mentions(string $html, string $linkClass = 'text-mention text-decoration-none'): string
    {
        // Allow usernames with dots in them (e.g. @brakus.alysha).
        // This pattern ensures the mention starts and ends with an alphanumeric/underscore/hyphen
        // while allowing dots inside the username. It avoids capturing trailing punctuation.
        return preg_replace_callback('/@([A-Za-z0-9_-](?:[A-Za-z0-9_.-]*[A-Za-z0-9_-])?)/', function ($m) use ($linkClass) {
            $username = $m[1];
            $user = \App\Models\User::where('username', $username)->first();
            if ($user) {
                $url = route('authors.show', $user->username);
                return '<a class="' . e($linkClass) . '" href="' . e($url) . '">@' . e($username) . '</a>';
            }
            return '@' . $username;
        }, $html);
    }
}
