<h3 id="main-site-text">YAMF Blog for Demo Purposes</h3>

<div id="author-controls">
    <a class="btn btn-default" href="<?= $app->basePath ?>/blog/write">Write post</a>
</div>
<hr>
<?php foreach ($posts as $post) { ?>
        <h4><a href="<?= $app->basePath ?>/blog/<?= $post->id ?>"><?= $post->title ?></a></h4>
        <p class="blog-preview"><?= $post->preview ?> | <a href="<?= $app->basePath ?>/blog/<?= $post->id ?>">Read me!</a></p>
        <hr>
<?php } ?>