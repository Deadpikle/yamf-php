<?php

namespace Controllers;

use Models\BlogPost;
use Yamf\models\ErrorMessage;
use Yamf\models\NotFound;
use Yamf\models\Request;
use Yamf\models\View;

class BlogController {

    public function index($app, Request $request) {
        $posts = BlogPost::load_all_posts();
        return new View('blog/index', compact('posts'), 'Blog Index');
    }

    public function view_post($app, Request $request) {
        $post = BlogPost::load_post($request->routeParams['id']);
        if ($post != null) {
            return new View('blog/post', compact('post'), 'Blog #' . ($post->id + 1));
        }
        else {
            return new NotFound();
        }
    }

    public function write_post($app, Request $request) {
        return new ErrorMessage("Can't let you write that blog post, StarFox!");
    }
}
