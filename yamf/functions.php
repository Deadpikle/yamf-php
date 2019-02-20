<?php

use Yamf\Models\Request;

/**
 * Checks to see if $haystack ends with $needle.
 * @param string $haystack
 * @param string $needle
 * @return bool
 */
function strEndsWith(string $haystack, string $needle): bool
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
function strStartsWith(string $haystack, string $needle): bool
{
    $length = strlen($needle);
    return strlen($haystack) >= $length && substr($haystack, 0, $length) === $needle;
}

/**
 * Removes empty strings ('') from an array.
 * Modifies original array
 */
function removeEmptyStringsFromArray(&$arr)
{
    for ($i = 0; $i < count($arr); $i++) {
        if ($arr[$i] === '') {
            array_splice($arr, $i, 1);
            $i--;
        }
    }
}

function yurl($app, string $path): string
{
    if (!strStartsWith($path, '/')) {
        return $app->basePath . '/' . $path;
    }
    return $app->basePath . $path;
}

function isPostRequest(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Returns NULL if no shorter URL found; destination as string if found
 * @param string $url
 * @param PDO $db
 * @return string
 */
function loadShortenedURL(string $url, $db): string
{
    if (!isset($db)) {
        return '';
    }
    $query = '
            SELECT ShortURLID, Destination, TimesUsed
            FROM ShortURLs
            WHERE Slug = ?';
    $stmt = $db->prepare($query);
    $params = [$url];
    $stmt->execute($params);
    $shortenedURLs = $stmt->fetchAll();
    if (count($shortenedURLs) > 0) {
        $item = $shortenedURLs[0];
        $timesUsed = (int)$item['TimesUsed'];
        $update = '
                UPDATE ShortURLs SET DateLastUsed = ?, TimesUsed = ?
                WHERE ShortURLID = ?';
        $params = [
            date('Y-m-d H:i:s'),
            $timesUsed + 1,
            $item['ShortURLID']
        ];
        $stmt = $db->prepare($update);
        $stmt->execute($params);
        return $item['Destination'];
    }
    return '';
}
