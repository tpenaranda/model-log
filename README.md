# Laravel ModelLog
A Laravel 5 package to automatically log attributes changes on any of your app models.

## About package
This package is intended for tracking changes of your Eloquent models inside your Laravel application.
A new DB table will be created and everytime a model attribute is updated an entry will be automatically created on the DB log table.

## Requirements

- Laravel 5

## Installation for Laravel 5.5 (package discovery support)

Install package using Composer (getcomposer.org).

    $ composer require tpenaranda/model-log

Run migrations to create ModelLog table.

    $ php artisan migrate

## Installation for Laravel 5 to 5.4

Install package using Composer (getcomposer.org).

    $ composer require tpenaranda/model-log

Add service provider and alias in config/app.php

```
    'providers' => [
        ...
        TPenaranda\ModelLog\Providers\ModelLogServiceProvider::class,
    ],
    'aliases' => [
        ...
        'ModelLogEntry' => TPenaranda\ModelLog\ModelLogEntry::class,
        'ObservedByModelLog' => TPenaranda\ModelLog\Traits\ObservedByModelLog::class,
    ],

```

Run  ModelLog command in order to create ModelLog DB table.

    $ php artisan model-log:create-log-table

## Usage

Add 'ObservedByModelLog' trait to your model and specify attributes you want to observe/track for changes.

```
class MyModel extends Model
{
    use \ObservedByModelLog

    protected $log = ['my_attribute', 'track_this_column_too'];
}
```

Now after every update on that model, observed attributes will be logged automatically.
Use `protected $log = 'all';` (notice the string, not array) to log any change.

Retrieve log entries:

```
$my_model->logEntries;
```


## Advanced usage

Create (or drop and create) ModelLog table manually:

    $ php artisan model-log:create-log-table


Flush ModelLog table:

```
\ModelLogEntry::flushAll();
```
