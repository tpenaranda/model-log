# Laravel ModelLog
A Laravel 5 package to automatically log attributes changes on any of your app models.

## About package
This package is intended for tracking changes of your Eloquent models inside your Laravel application.
A new DB table will be created and anytime a model attribute is updated an entry is automatically made on the new log table.

## Installation

Require this package in your composer.json

    $ composer require tpenaranda/model-log

Run artisan command to generate the migration for the new log table.

    $ php artisan model-log:create-log-table

Add trait and specify attributes you want to observe/track for changes.

```
    class MyModel extends Model
    {
        use \EnableModelLog;

        protected $log = ['my_attribute', 'track_this_column_too'];
    }
```

## Usage

Retrieve log entries:

```
        $my_model->logEntries
```
