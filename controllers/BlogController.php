<?php

namespace Controllers;

use Models\BlogPost;
use Yamf\Models\ErrorMessage;
use Yamf\Models\NotFound;
use Yamf\Models\Request;
use Yamf\Models\View;

class BlogController
{
    public function index($app, Request $request)
    {
        $posts = BlogPost::loadAllPosts();
        return new View('blog/index', compact('posts'), 'Blog Index');
    }

    public function viewPost($app, Request $request)
    {
        $post = BlogPost::loadPost($request->routeParams['id']);
        if ($post != null) {
            return new View('blog/post', compact('post'), 'Blog #' . ($post->id + 1));
        } else {
            return new NotFound();
        }
    }

    public function writePost($app, Request $request)
    {
        return new ErrorMessage("Can't let you write that blog post, StarFox!");
    }
}
