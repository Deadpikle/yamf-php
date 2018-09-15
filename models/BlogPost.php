<?php

class BlogPost {

    const NUMBER_OF_POSTS = 4; // arbitrary number as we aren't using a database (https://xkcd.com/221)

    public $id;
    public $title;
    public $preview;
    public $content;

    public function __construct($id, $title, $preview, $content) {
        $this->id = $id;
        $this->title = $title;
        $this->preview = $preview;
        $this->content = $content;
    }

    public static function load_post($id) {
        // In a real app, you'd be returning stuff from your database or file system, probably! :)
        if (!is_numeric($id) || $id < 0 || $id >= BlogPost::NUMBER_OF_POSTS) {
            return null;
        }
        return new BlogPost($id, 'Hello Post ' . ($id + 1) . '!', 
            'This is blog post #' . ($id + 1), 
            'I am the content of blog post #' . ($id + 1) . '. Thanks for checking me out!');
    }

    public static function load_all_posts() {
        $posts = [];
        for ($i = 0; $i < BlogPost::NUMBER_OF_POSTS; $i++) {
            $posts[] = BlogPost::load_post($i);
        }
        return $posts;
    }

}

?>