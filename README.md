# Laravel ModelLog
A Laravel 5 package to automatically log attributes changes on any of your app models.

## About package
This package is intended for tracking changes of your Eloquent models inside your Laravel application.
A new DB table will be created and everytime a model attribute is updated an entry will be automatically created on the DB log table.

## Requirements

- Laravel 5

## Installation for Laravel 5.5 (package discovery support)

Install package using [Composer](getcomposer.org).

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

Retrieve log entries using query scopes:

```
\ModelLogEntry::whereModelClass('App\MyModel')->whereAttribute('my_attribute')->whereTo('value_after_change')->get();
```

Available scopes:

- whereModel(`<object>`): _Get logs of an specific Eloquent Model_ (example: get log data of MyModel ID #4).
- whereModelClass(`<string/object>`): _Get logs for an specific model class_ (example: get entries where MyModel class is involved, regardless of any IDs).
- whereAttribute(`<string>`): _Get only logs where some specific attribute was changed._
- whereFrom(`<string>`): _Get only logs with an specific initial value._
- whereTo(`<string>`): _Get only logs with an specific end value._
- ModifiedByUser(`<numeric/object>`): _Get changes done by some specific user._ Allowed parameters: null, numeric IDs or User object.
The following scopes only accept [Carbon](carbon.nesbot.com) objects as parameters:
- loggedBefore(`<Carbon object>`): _Retrieve only entries logged prior to specific date._
- loggedAfter(`<Carbon object>`): _Retrieve only entries logged after specific date._
- withinDateRange(`<Carbon object>`, `<Carbon object>`): _Retrieve only entries logged after first parameter and prior to second parameter._

Create (or drop and create) ModelLog table manually:

    $ php artisan model-log:create-log-table

Flush ModelLog table:

```
\ModelLogEntry::flushAll();
```
