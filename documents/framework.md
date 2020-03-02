# The PHP Framework
The Banq website and backend server are written in PHP with a self-made nameless framework. The framework consists of a few standard 'core' classes and an [MVC](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller) way to divide the project into models, controllers and views. The class names and other functions are similar to the well-known PHP framework [Laravel](https://laravel.com/docs/).

## The building blocks
The framework uses the Model–view–controller design pattern:

- The **core classes** are a few classes each with a specific purpose:
    - The Auth class contains a complete Authenticatie system.
    - The Database class contains a nice wrapper around [PDO](https://www.php.net/manual/en/book.pdo.php).
    - The Model class is the base class for every Model and contains handy query functions.
    - The `parse_user_agent.php` file contains a function that parses a browser user agent so you can get the name, version and platform name in a nice manner.
    - The Router class contains a very powerfull router which also does some light route model binding.
    - The Session class is a wrapper around the [PHP session functions](https://www.php.net/manual/en/book.session.php) which also had a flash functionality.
    - The `utils.php` file contains the validation and view functions and some more small utilities.

- A **model** is a class that is linked to a database table. The class contains all the functions that execute qeuries on that table. It also contains the create table SQL code and, where necessary, a fill initialization function. Each model extends the Model 'core' class this class already contains many standard query functions, covering approximately 80 to 90% of all your requirements. So you only have to add a few more specific query functions. Finally, the model also contains all validation rules of the fields that are in the database table.

- A **controller** is a class where each function is a separate page that is described in the `routes/` files. The task of the controller is to validate and process all incoming information from, for example, form fields. If needed, the controller can also pass information to a view so that an HTML page can be generated.

- A **view** is a special HTML file which is actually a PHP file, but where a simple translation is made by a few regular expressions, this ensures that you can write much nicer template code that remains legible. The view took the data from the controller and turns it into an HTML page that is then sent back to the user.

- A **resource** are CSS or JavaScript files where you can use the special PHP view template language and which are compiled / minified by going to the route `/debug/compile`.

## The view template language
In the view / template HTML files you can use a special PHP syntax, here below are some examples:

### A simple variable
```php
<h1>Hello, {{ $name }}!</h1>
```
```php
<h1>Hello, <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>!</h1>
```

### A simple foreach loop
```php
<ul>
    @foreach ($posts as $post):
        <li>
            <h2>{{ $post->name }}</h2>
            <p>{!! $post->body !!}</p>
        </li>
    @endforeach
</ul>
```
```php
<ul>
    <?php foreach ($posts as $post): ?>
        <li>
            <h2><?php echo htmlspecialchars($post->name, ENT_QUOTES, 'UTF-8') ?></h2>
            <p><?php $post->body ?></p>
        </li>
    <?php endforeach ?>
</ul>
```

As you can see is the syntax a lot nicer than using standard PHP, and you get [Cross-site scripting](https://en.wikipedia.org/wiki/Cross-site_scripting) prevention for free extra! But if you don't want to escape your data you can uses the exclamation marks in the syntax, but be warned because this is not safe.
