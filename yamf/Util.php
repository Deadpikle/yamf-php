<?php

namespace Yamf;

class Util
{
    /**
     * Checks to see if $haystack ends with $needle.
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function strEndsWith(string $haystack, string $needle) : bool
    {
        $length = strlen($needle);
        return $length === 0 || (substr($haystack, -$length) === $needle);
    }

    /**
     * Checks to see if $haystack starts with $needle.
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function strStartsWith(string $haystack, string $needle) : bool
    {
        $length = strlen($needle);
        return strlen($haystack) >= $length && substr($haystack, 0, $length) === $needle;
    }

    public static function isPostRequest() : bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Removes empty strings ('') from an array.
     * Modifies original array
     */
    public function removeEmptyStringsFromArray(&$arr)
    {
        for ($i = 0; $i < count($arr); $i++) {
            if ($arr[$i] === '') {
                array_splice($arr, $i, 1);
                $i--;
            }
        }
    }
}
