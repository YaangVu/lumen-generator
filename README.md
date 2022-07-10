# Lumen Generator

[![Total Downloads](https://poser.pugx.org/yaangvu/lumen-generator/d/total.svg)](https://packagist.org/packages/yaangvu/lumen-generator)
[![Latest Stable Version](https://poser.pugx.org/yaangvu/lumen-generator/v/stable.svg)](https://packagist.org/packages/yaangvu/lumen-generator)
[![Latest Unstable Version](https://poser.pugx.org/yaangvu/lumen-generator/v/unstable.svg)](https://packagist.org/packages/yaangvu/lumen-generator)
[![License](https://poser.pugx.org/yaangvu/lumen-generator/license.svg)](https://packagist.org/packages/yaangvu/lumen-generator)

Do you miss any Laravel code generator on your Lumen project?
If yes, then you're in the right place.

## Installation

To use _some_ generators command in Lumen (just like you do in Laravel), you need to add this package:

```sh
composer require yaangvu/lumen-generator
```

## Configuration

Inside your `bootstrap/app.php` file, add:

```php
$app->register(YaangVu\LumenGeneratorServiceProvider::class);
```

## Available Command

```
key:generate         Set the application key

yaang:cast            Create a new custom Eloquent cast class
yaang:channel         Create a new channel class
yaang:command         Create a new Artisan command
yaang:controller      Create a new controller class
yaang:event           Create a new event class
yaang:exception       Create a new custom exception class
yaang:factory         Create a new model factory
yaang:job             Create a new job class
yaang:listener        Create a new event listener class
yaang:mail            Create a new email class
yaang:middleware      Create a new middleware class
yaang:migration       Create a new migration file
yaang:model           Create a new Eloquent model class
yaang:notification    Create a new notification class
yaang:pipe            Create a new pipe class
yaang:policy          Create a new policy class
yaang:provider        Create a new service provider class
yaang:request         Create a new form request class
yaang:resource        Create a new resource
yaang:rule            Create a new rule
yaang:seeder          Create a new seeder class
yaang:test            Create a new test class

notifications:table  Create a migration for the notifications table

schema:dump          Dump the given database schema
```

## Additional Useful Command

```
clear-compiled    Remove the compiled class file
serve             Serve the application on the PHP development server
tinker            Interact with your application
optimize          Optimize the framework for better performance
route:list        Display all registered routes.
```

> **NOTES** `route:list` command has been added via [appzcoder/lumen-route-list](https://github.com/appzcoder/lumen-route-list) package.

## Tinker `include` Argument Usage

`php artisan tinker path/to/tinker/script.php`

script.php example:
```
$environment = app()->environment();
$output = new Symfony\Component\Console\Output\ConsoleOutput();
$output->writeln("<info>Hello the app environment is `{$environment}`</info>");
$output->writeln("<comment>Did something</comment>");
$output->writeln("<error>Did something bad</error>");
```
