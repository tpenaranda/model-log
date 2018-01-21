# Laravel ModelLog
A Laravel 5 package to automatically log attributes changes on any of your app models.

## About package
This package is intended for tracking changes of your Eloquent models inside your Laravel application.
A new DB table will be created and everytime a model attribute is updated an entry will be automatically created on the DB log table.

## Installation

Install package using Composer (getcomposer.org).

    $ composer require tpenaranda/model-log

Run migrations to create ModelLog table.

    $ php artisan migrate

Add trait and specify attributes you want to observe/track for changes.

```
class MyModel extends Model
{
    use \ModelLog;

    protected $log = ['my_attribute', 'track_this_column_too'];
}
```

## Usage

Retrieve log entries:

```
$my_model->logEntries;
```


## Advanced usage

Create (or drop) ModelLog table manually:

    $ php artisan model-log:create-log-table


Flush ModelLog table.

```
\ModelLog::flushAll()
```
