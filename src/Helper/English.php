<?php

namespace WishgranterProject\MusicRadar\Helper;

abstract class English
{
    /**
     * Returns a given string in the singular.
     *
     * @param string $string
     *   A string in the plural.
     *
     * @return string
     *   The string in singular.
     */
    public static function unpluralize(string $string): string
    {
        $words = explode(' ', $string);

        $singular = array_map([English::class, 'singular'], $words);

        return implode(' ', $singular);
    }

    /**
     * Returns a given word in its singular form.
     *
     * @param string $word
     *   A word in the plural.
     *
     * @return string
     *   The word in singular.
     */
    protected static function singular(string $word): string
    {
        $rules = [
            '/ies$/' => 'y', // 'batteries' -> 'battery'
            '/es$/'  => '',  // 'boxes' -> 'box'
            '/s$/'   => '',  // 'cats' -> 'cat'
        ];

        foreach ($rules as $pattern => $replacement) {
            if (preg_match($pattern, $word)) {
                return preg_replace($pattern, $replacement, $word);
            }
        }

        return $word;
    }
}
