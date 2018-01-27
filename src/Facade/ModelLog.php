<?php

namespace TPenaranda\ModelLog\Facade;

use Illuminate\Support\Facades\Facade;

class ModelLog extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'model-log';
    }
}
