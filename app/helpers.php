<?php

if (! function_exists('linkify_mentions')) {
    /**
     * Convert @username mentions into profile links when the user exists.
     * Safe to call from views and controllers.
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

                return '<a class="'.e($linkClass).'" href="'.e($url).'">@'.e($username).'</a>';
            }

            return '@'.$username;
        }, $html);
    }
}

if (! function_exists('mask_profanity')) {
    /**
     * Mask profane words in a string using the configured word list.
     * Uses Str::mask() to fully hide matched tokens, case-insensitive with Unicode.
     */
    function mask_profanity(string $text): string
    {
        $words = config('profanity.words', []);

        if (empty($words)) {
            return $text;
        }

        $escaped = array_map(static fn (string $w): string => preg_quote($w, '/'), $words);
        $pattern = '/\b(?:'.implode('|', $escaped).')\b/iu';

        $masked = preg_replace_callback(
            $pattern,
            static fn (array $m): string => \Illuminate\Support\Str::mask($m[0], '*', 0),
            $text
        );

        return $masked ?? $text;
    }
}
