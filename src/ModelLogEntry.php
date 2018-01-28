<?php

namespace TPenaranda\ModelLog;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

    function isSerialized($value)
    {
        if (!is_string($value)) {
            return false;
        }

        return preg_match('/^([adObis]:|N;)/', $value);
    }

class ModelLogEntry extends Model
{
    protected $table = 'tpenaranda_model_log_log_entries';

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
        return isSerialized($this->attributes['from']) ? unserialize($this->attributes['from']) : $this->attributes['from'];
    }

    public function getToAttribute()
    {
        return isSerialized($this->attributes['to']) ? unserialize($this->attributes['to']) : $this->attributes['to'];
    }

    public function isSerialized($value)
    {
        return is_string($value) && preg_match('/^([adObis]:|N;)/', $value);
    }

    public function unserializeData()
    {
        $this->to = $this->getToAttribute();
        $this->from = $this->getFromAttribute();

        return $this;
    }

    public static function flushAll()
    {
        return self::all()->each->forceDelete();
    }

    public function scopeForModel(Builder $builder, $model)
    {
        return $builder->where('model_name', get_class($model))->where('model_foreign_key', $model->id);
    }
}
