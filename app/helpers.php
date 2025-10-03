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

                return '<a class="' . e($linkClass) . '" href="' . e($url) . '">@' . e($username) . '</a>';
            }

            return '@' . $username;
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

        $escaped = array_map(static fn(string $w): string => preg_quote($w, '/'), $words);
        $pattern = '/\b(?:' . implode('|', $escaped) . ')\b/iu';

        $masked = preg_replace_callback(
            $pattern,
            static function (array $m): string {
                $word = $m[0];
                $len = mb_strlen($word);
                if ($len <= 2) {
                    return str_repeat('*', $len);
                }
                $first = mb_substr($word, 0, 1);
                $last = mb_substr($word, -1);
                $middle = str_repeat('*', $len - 2);
                return $first . $middle . $last;
            },
            $text
        );

        return $masked ?? $text;
    }
}
