<?php
    /**
     * Checks to see if $haystack ends with $needle.
     */
    function ends_with($haystack, $needle) {
        $length = strlen($needle);
        return $length === 0 || (substr($haystack, -$length) === $needle);
    }

    /**
     * Removes empty strings ('') from an array.
     * Modifies original array
     */
    function remove_empty_strings_from_array(&$arr) {
        for ($i = 0; $i < count($arr); $i++) {
            if ($arr[$i] === '') {
                array_splice($arr, $i, 1);
                $i--;
            }
        }
    }

    function isPostRequest() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /// Returns NULL if no shorter URL found; destination as string if found
    function loadShortenedURL($url, $db) {
        if (!isset($db)) {
            return '';
        }
        $query = '
            SELECT ShortURLID, Destination, TimesUsed
            FROM ShortURLs
            WHERE Slug = ?';
        $stmt = $db->prepare($query);
        $params = [ $url ];
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

    /**
     * Attempts to find a route (as defined in routes.php) that works for
     * the given request string.
     * Returns null if it didn't find a route.
     */
    function find_route($routes, $request) {
        $isPost = isPostRequest();

        // Need to make sure # and ? in URL are handled properly and one doesn't interfere with the other!
        $anchorOnPage = parse_url($request, PHP_URL_FRAGMENT); // http://.../blog#comments (#comments)
        if ($anchorOnPage !== null) {
            // remove the anchor from the request so we handle everything properly
            $anchorLocation = strrpos($request, "#"); // strrpos = searches backwards
            $request = substr($request, 0, $anchorLocation);
        }

        // pull out get parameters if there are any
        $getParamLocation = strpos($request, '?');
        $getParams = [];
        if ($getParamLocation !== false) {
            // parse out GET params
            if ($getParamLocation + 1 < strlen($request)) {
                $getStr = substr($request, $getParamLocation + 1);
                parse_str($getStr, $getParams); // getParams now has the get parameters
            }
            $request = substr($request, 0, $getParamLocation);
        }

        // now parse the actual request and find its route
        $requestParts = explode('/', $request);
        remove_empty_strings_from_array($requestParts);
        $numberOfRequestParts = count($requestParts);
        // find the route
        foreach ($routes as $route => $path) {
            $routeParts = explode('/', $route);
            // remove empty request parts for ease of figuring out where we are
            remove_empty_strings_from_array($routeParts);
            // a matching route will have the same number of parts
            if (count($routeParts) !== $numberOfRequestParts) {
                continue;
            }
            // make sure GET or POST matches up
            // first check to see if they defined multiple routes for a single route (e.g. GET and POST)
            if (count($path) > 0 && is_array($path[0])) {
                // potentially multiple GET/POST routes defined for this route
                foreach ($path as $potentialPath) {
                    if (count($potentialPath) === 3) { // safety
                        if (strtolower($potentialPath[0]) == 'get' && !$isPost) {
                            $path = $potentialPath; 
                            break;
                        }
                        else if (strtolower($potentialPath[0]) == 'post' && $isPost) {
                            $path = $potentialPath;
                            break;
                        }
                    }
                }
            }
            else if (count($path) == 2 && $isPost) {
                continue; // didn't match up as the route is a GET route
            }
            else if (count($path) === 3) {
                if (strtolower($path[0]) === 'get' && $isPost) {
                    continue; 
                }
                else if (strtolower($path[0]) === 'post' && !$isPost) {
                    continue;
                }
            }
            
            $didFind = true;
            $foundParams = [];
            // ok, we have the same number of parts in the url; now see if the url text lines up
            for ($i = 0; $i < $numberOfRequestParts; $i++) {
                $matches = [];
                // we allow for "parameters" in the url as defined by { } in the route definition
                // not to be confused with GET params, which come after the ? part of the URL!
                // regex from https://stackoverflow.com/a/413165/3938401
                $routeParseResult = preg_match("/\{([^}]+)\}/", $routeParts[$i], $matches);
                if ($routeParseResult !== 0 && $routeParseResult !== false) {
                    // found a match! match will be in array location 1
                    $foundParams[$matches[1]] = $requestParts[$i];
                }
                else if ($requestParts[$i] !== $routeParts[$i]) {
                    $didFind = false;
                    break;
                }
            }
            // found it? return the output!
            if ($didFind) {
                $output = new Request();
                $output->route = $route;
                if (count($path) == 2) {
                    $output->controller = $path[0];
                    $output->function = $path[1];
                }
                else if (count($path) == 3) {
                    $output->controller = $path[1];
                    $output->function = $path[2];
                }
                $output->routeParams = $foundParams;
                $output->get = $getParams;
                $output->anchor = $anchorOnPage;
                $output->post = $_POST;
                return $output;
            }
        }
        return null;
    }

?>