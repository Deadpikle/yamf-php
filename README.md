# YAMF

YAMF (Yet Another MVC Framework) is a small PHP MVC framework designed to let you start using the MVC paradigm quickly without a lot of setup work. All you need is a webserver that accepts `.htaccess` files and you're pretty much good to go!

## Disclaimer

This project is by no means a display of "perfect" PHP use or even the MVC paradigm. For example, this project isn't currently using PSR-4, and the YAMF classes are not namespaced at the moment. That's OK! You can still use this project to help you get your website up and running quickly without having to worry about the potentially steep learning curve of a big framework. At the very least, you get some free code organization and pretty URLs that don't have the annoying `.php` at the end!

## Setup

Minimum requirements: PHP 7.0+.

1. Clone the repository.
2. Make sure your web server is setup to read and utilize .htaccess files. (If you don't know what this means, please Google it! Maybe we'll add some helper links here at a later time.)
3. Copy `config.sample.php` to `config.php`. Adjust any parameters that you want in there. (You don't need to adjust anything by default to get your site up and running.)
4. You're done! Enjoy your MVC-based website! You can rip out the default sample items and CSS, of course!

## Wait! Where's the database migration library / view templating logic / unit testing library / {other framework here}?!

They're not here, and they're not here on purpose! YAMF was purposefully written to not use any external dependencies to lower the barrier of entry to using the MVC paradigm in PHP. If you want any of those things, just pull them in with your favorite dependency manager (like [Composer](https://getcomposer.org)). That way, everyone can use their own favorite libraries! Do note that `vendor/autoload.php` is automatically imported for you if you choose to use `Composer`.

For a note regarding view templating, see the section on subclassing.

## Documentation

If you read the following documentation on YAMF, you'll likely be much better off than just hopping in and trying to read the code yourself. :) It will only take a few minutes, and it's worth it!

### Adding a new route

The first thing you'll probably want to do is add a new route. To do that, follow these steps:

1. If you don't already have a controller class for your new route, go ahead and add one in the `controllers` folder of the project. Subfolders are OK. The controller doesn't actually have to have `Controller` in the name, but make sure the class name matches the file name.
2. Add a stub for your controller method. At the very least, it must be a `public` function that takes two parameters: `$app` and `$request` (in that order).
3. Open up `routes.php` and make sure you understand the documentation and examples of how routes work.
4. Add your route to the `routes` array. 
    * Make sure to designate `POST` if necessary. 
    * The controller name string has to match the file name and class name for the controller from step one. 
    * If the controller is in a subfolder (or subfolders), make sure you have the name in a `Parent-Folder/Controller-Name` format. 
    * The function name should match the function name from step 2.
5. At this point, the route is working, but your controller method needs to return some kind of `yamf/models/Response`, which can be anything from a `View` to a JSON response to, well, whatever you want, really. See the next section for more info here. 
6. Your normal use case is probably returning a `yamf/models/View`. Create a PHP file (view) in the `views` folder -- again, subfolders are OK -- and, in your controller method, `return new View('name/of/view');`.
7. The route should now be functional! 

In short:

1. Add controller and controller method to the `controllers` folder
2. Add route to `routes.php`
3. Create view in `views` folder if necessary
4. Modify controller method to `return new View('name/of/view');` 

### Return types from controller functions

All controller methods that are called as a result of a route should return a `yamf/models/Response` or a subclass of said class. Here are some descriptions on how to use each one. Most can be used in one line with just the constructor.

* `Response` -- the base class for a response from a route. 
    * Constructor format: `new Response($statusCode = 200)` 
    * Outputs the status code.
    * Since all other `yamf/models/Response` items subclass from this parent class, they can all return a custom HTTP status code, if you want!
* `ErrorMessage` -- simple wrapper around View to send back a 400 status code and the error.php view.
    * Constructor format: `new ErrorMessage($msg = '', $name = 'error', $title = '', $headerName = '', $footerName = '')`. `$name` is the name of the view.
    * Note that the first parameter is the error message, not the name of the view!
    * By default, the variable name for the error to output in your view is `$error`. You can't change this without subclassing.
    * By default, the header and footer used are `views/header.php` and `views/footer.php`.
    * The status code can, of course, be changed.
* `JsonResponse` -- example on how you can send back a JSON response from your controller 
    * Constructor format: `new JsonResponse($data, $jsonEncodeOptions = 0)`
    * Outputs `$data` via `echo json_encode($this->data, $this->jsonEncodeOptions);`
* `NotFound` -- used for returning a 404 not found message back to the client.
    * Constructor format: `new NotFound($name = '404', $data = [], $title = '', $headerName = '', $footerName = '')`. `$name` is the name of the view.
    * By default, the header and footer used are `views/header.php` and `views/footer.php`.
    * If you want to send back just a 404 (e.g. for an API), use `Response`.
* `Redirect` -- allows for a 302 redirect to some other page or route
    * Constructor format: `new Redirect($redirectPath, $isInternalRedirect = true)`
    * Status code is 302 by default
    * `$isInternalRedirect` is used to redirect to a route **within the current website/`routes.php` data**. If you want to redirect to an internal route, use `Redirect` like this: `new Redirect('/route/name);` (note the starting `/`). If not, use `Redirect` like this: `new Redirect('https://example.com', false);`.
* `View` -- used for returning some PHP-based HTML view. 
    * Constructor format: `new View($name, $data = [], $title = '', $headerName = '', $footerName = '')`. `$name` is the name of the view.
    * To send data to your view, the easiest way is probably to use the `compact` function. See the sample's `BlogController`.
    * By default, the header and footer used are `views/header.php` and `views/footer.php`.

If you have ideas for more `Response` types that should be included in YAMF, please open a feature request (via the Issues tab) or open a Pull Request!

### Data sent into controller methods

Two main variables are sent into your controller methods from the route: `$app` and `$request`. **Note that all `View` output will have the `$app` and `$request` variables available to use. You do not need to send them as a `$data` parameter.**

#### `$app`

`$app` is mainly configuration variables that you've set up in `config.php`. Check that file out for what's available. Some of the more important items are:

* `$app->db` for a database connection
* Default header file names for all views, the 404 page, and the error page. You can set these to `null` to avoid using them at all.

In addition to those, there are two variables available that are set up in `init.php`:

* `$app->isLocalHost` -- whether or not the application is running on local host (`127.0.0.1` or `::1`)
* `$app->basePath` -- the base directory for your current web application. This can be used to allow for nested web applications on your web server. I *strongly* recommend making use of this variable in your `views` when doing local website links so that if you move files around or later move things into a subfolder everything doesn't break. To link to another page on your site, just use the `yurl` (YAMF URL) function like this: `<?= yurl($app, '/path/to/page') ?>`.

#### `$request`

`$request` is going to have all your data about your route and the different parameters that have come in with your request. It's of type `yamf/models/Request`. It has these public members available:

* `$request->route` -- raw route string for this request
* `$request->controller` -- string name of controller
* `$request->function` -- string name of controller function to call
* `$request->routeParams` -- any params in the route such as {id}. Format: ['id' => value]
* `$request->get` -- any GET params found in the URL -- same format as $_GET (no extra processing performed)
* `$request->post` -- any POST params -- same format as $_POST (no extra processing performed)
* `$request->anchor` -- If used, the # portion of the url (without the #). Router is smart enough to not match on URLs like `/blah/#/foo`.

### Subclassing for extending the framework

The easiest way for you to extend this framework is to derive from `Response` (or some other `Response` child class) and/or create a parent class for some of your controllers. For example, by deriving from `View`, you could change the view output to use [Twig](https://twig.symfony.com/) templating engine instead of just simple PHP output. By creating a parent class for your controllers, you could add things like validation or other extra processing of data that has to happen for all of your API routes, such as verifying a username/password or token. The options are endless and the potential great!

### Static page simplicity

One nifty feature that YAMF supports is static web pages that don't require a `route` or a `controller`. If you want `/about` to just be a simple page, throw an `about.php` page in the `views/static/` folder and -- bam! -- `/about` works on your website. What about subfolders like `/blog/post-name`? That works too! Add a `views/static/blog/post-name.php` file and it Just Works (tm)! You can use this to still have pretty URLs on your website without bothering with adding routes and controllers.

**Note that the router attempts to match a `router.php` route before checking for static pages.**

### URL Shortener

A feature that the router supports that is not immediately enabled is URL shortening -- e.g. `https://example.com/short-url`. It requires a database connection, but if you add the database connection and have the appropriate database table (the schema is in `config.sample.php`), URL shortening can be used if you want. You're not required to enable this feature.

### Default session logic

I've included some default session logic commented out in `config.sample.php`. Feel free to use it, modify it, or throw it out. If you have suggestions on how to improve this functionality, please open a pull request or an issue to generate further discussion.

## Can I help contribute?

Glad you asked! There are always things that can be done on an open-source project: fix bugs, new features, and more! Check out the issues tab of this repository and take a look at what bugs have been reported and which features have been requested. There's some more info on contributing in the [Contributing](CONTRIBUTING.md) document.

## License

MIT License. Please make sure to include the license for this framework (along with a GitHub link if you're feeling generous!) when using it on your site. Thank you! :)

## Special Thanks

Special thanks to [Bootswatch](https://bootswatch.com/) for the [Bootstrap](https://getbootstrap.com/docs/3.3/) [Paper theme](https://bootswatch.com/3/paper/) used in the sample.