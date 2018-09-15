<?php

require_once 'yamf/models/View.php';
require_once 'yamf/models/NotFound.php';
require_once 'yamf/models/ErrorMessage.php';

require_once 'models/BlogPost.php';

class BlogController {

    public function index($app, $request) {
        $posts = BlogPost::load_all_posts();
        return new View('blog/index', compact('posts'), 'Blog Index');
    }

    public function view_post($app, $request) {
        $post = BlogPost::load_post($request->routeParams['id']);
        if ($post != null) {
            return new View('blog/post', compact('post'), 'Blog #' . ($post->id + 1));
        }
        else {
            return new NotFound();
        }
    }

    public function write_post($app, $request) {
        return new ErrorMessage("Can't let you write that blog post, StarFox!");
    }
}

?>