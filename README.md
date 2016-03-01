# Lemmon Router

**Lemmon Router** is a lightweight standalone routing library for PHP 7.

* Flexible regular expression routing
* Simple and lightweight
* RESTful routing
* Fast (100k requests/second)
* Mod_Rewrite is not required, although supported; extreme portability for rapid development

## Getting started

1. PHP 7.x is required
2. Install Lemmon Router using [Composer](#composer-installation) (recommended) or manually

## Composer Installation

1. Get [Composer](http://getcomposer.org/)
2. Require Lemmon Router with `php composer.phar require lemmon/router`
3. Add the following to your application's main PHP file: `require 'vendor/autoload.php';`

## Examples

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$r = new Lemmon\Router\Router;
$r->match('hello-world', function() {
    echo 'Hello World!';
});
$r->dispatch();
```

*Example 1* - Respond to all requests

```php
$r->match(function() {
    # matches everything
});
```

*Example 2* - Match parameters

```php
$r->match('{controller}/{action}', function($r) {
	# $r->controller;
	# $r->action;
});
```

*Example 3* - RESTful routing

```php
$r->match(['GET', 'PUT'], '{controller}(/(?<trail>{action:read|write}/{id:num:1,3=1})!)', ['controller' => '\w+'], function($r) {
    # matches only GET and PUT requests
    # matches either 'controller' or 'controller/action/id' when route conditions are met
    # controller can be any word
    # action is either read or write
    # id must be numeric of length 1 to 3, default is 1
});
```
