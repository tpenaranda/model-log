<?php

namespace TPenaranda\ModelLog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

class ModelLog extends Model
{
    protected $table = 'tpenaranda_model_log_logs';

    protected $fillable = [
        'attribute',
        'from',
        'model_foreign_key',
        'model_name',
        'to',
        'updated_by_user_id',
    ];

    public function getFromAttribute()
    {
        return is_string($this->attributes['from']) ? unserialize($this->attributes['from']) : null;
    }

    public function getToAttribute()
    {
        return is_string($this->attributes['to']) ? unserialize($this->attributes['to']) : null;
    }

    public static function flushAll()
    {
        return self::all()->forceDelete();
    }
}
