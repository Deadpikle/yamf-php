<?php

namespace Controllers;

use Models\BlogPost;
use Yamf\AppConfig;
use Yamf\Responses\ErrorMessage;
use Yamf\Responses\NotFound;
use use Yamf\Request;
use Yamf\Responses\View;

class BlogController
{
    public function index(AppConfig $app, Request $request)
    {
        $posts = BlogPost::loadAllPosts();
        return new View('blog/index', compact('posts'), 'Blog Index');
    }

    public function viewPost(AppConfig $app, Request $request)
    {
        $post = BlogPost::loadPost($request->routeParams['id']);
        if ($post != null) {
            return new View('blog/post', compact('post'), 'Blog #' . ($post->id + 1));
        } else {
            return new NotFound();
        }
    }

    public function writePost(AppConfig $app, Request $request)
    {
        return new ErrorMessage("Can't let you write that blog post, StarFox!");
    }
}
