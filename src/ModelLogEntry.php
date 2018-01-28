<?php

namespace TPenaranda\ModelLog;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use TPenaranda\ModelLog\Exceptions\ClassNotFoundException;
use TPenaranda\ModelLog\Exceptions\InvalidClassException;

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
        return $this->isSerialized($this->attributes['from']) ? unserialize($this->attributes['from']) : $this->attributes['from'];
    }

    public function getToAttribute()
    {
        return $this->isSerialized($this->attributes['to']) ? unserialize($this->attributes['to']) : $this->attributes['to'];
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

    public function scopeWhereModel(Builder $builder, $model)
    {
        return $builder->where('model_name', get_class($model))->where('model_foreign_key', $model->id);
    }

    public function scopeWhereModelClass(Builder $builder, $class_name)
    {
        $class_name = ($class_name instanceof Model) ? get_class($class_name) : trim($class_name, '\\');

        if (!class_exists($class_name)) {
            throw new ClassNotFoundException("Class \"{$class_name}\" does not exists.");
        }

        return $builder->where('model_name', $class_name);
    }

    public function scopeWhereAttribute(Builder $builder, $attribute)
    {
        return $builder->where('attribute', $attribute);
    }

    public function scopeWhereFrom(Builder $builder, $from)
    {
        return $builder->where('from', serialize($from));
    }

    public function scopeWhereTo(Builder $builder, $to)
    {
        return $builder->where('to', serialize($to));
    }

    public function scopeWithinDateRange(Builder $builder, Carbon $start_date, Carbon $end_date)
    {
        return $builder->createdAfter($start_date)->createdBefore($end_date);
    }

    public function scopeLoggedBefore(Builder $builder, Carbon $date)
    {
        return $builder->where('created_at', '<=', $date);
    }

    public function scopeLoggedAfter(Builder $builder, Carbon $date)
    {
        return $builder->where('created_at', '>=', $date);
    }

    public function scopeModifiedByUser(Builder $builder, $user)
    {
        if (is_numeric($user)) {
            $user_id = (int) $user;
        } elseif (is_object($user) && get_class($user) === auth()->getProvider()->getModel()) {
            $user_id = $user->id;
        } elseif (empty($user)) {
            $user_id = null;
        } else {
            throw new InvalidClassException('Invalid argument supplied for modifiedByUser query scope.');
        }

        return $builder->where('updated_by_user_id', $user_id);
    }
}
