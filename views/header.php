<?php
    $currentRequest = str_replace($app->basePath, '', $_SERVER['REQUEST_URI']);
    if (ends_with($currentRequest, '/')) {
        $currentRequest = substr($currentRequest, 0, -1);
    }
    $currentRequest = str_replace("/", "", $currentRequest);

    function navbar_link($name, $path, $basePath, $currentRequest) {
        // figure out whether the current navbar item is active or not based on the URL
        $isCurrent = FALSE;
        $pathNoSlashes = str_replace("/", "", $path);
        if ($path === "/" && $currentRequest === "") {
            $isCurrent = TRUE;
        }
        else if ($currentRequest !== "" && $pathNoSlashes != "" && strpos($currentRequest, $pathNoSlashes) !== FALSE) {
            $isCurrent = TRUE;
        }
        $liClass = $isCurrent ? "active" : "";
        return '<li class="' . $liClass . '"><a href="' . $basePath . $path . '">' . $name . '</a></li>';
    }

    $title = isset($title) ? trim($title) . " - YAMF" : "YAMF";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="<?= $app->basePath ?>/css/bootstrap.min.css"> <!-- from https://bootswatch.com/ -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

        <link rel="stylesheet" href="<?=$app->basePath?>/css/common.css?<?= filemtime("css/common.css") ?>" />

        <meta property="og:title" content="<?= $title ?>" />
        <meta property="og:site_name" content="YAMF" />
        <meta property="og:locale" content="en_US" />

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        
        <title><?= $title ?></title>
    </head>
    <body>
        <header>
            <nav class="navbar navbar-default">
                <div class="container container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    <a class="navbar-brand" href="<?=$app->basePath == "" ? "/" : $app->basePath ?>">YAMF</a>
                    </div>
                    <div class="navbar-collapse collapse" id="navbar">
                        <ul class="nav navbar-nav">
                            <?= navbar_link("Home", "/", $app->basePath, $currentRequest) ?>
                            <?= navbar_link("Blog", "/blog", $app->basePath, $currentRequest) ?>
                            <?= navbar_link("About", "/about", $app->basePath, $currentRequest) ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main>
            <div class="container">