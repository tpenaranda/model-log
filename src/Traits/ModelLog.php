<?php

namespace TPenaranda\ModelLog\Traits;

use TPenaranda\ModelLog\ModelLog as ModelLogModel;
use ReflectionClass;

trait ModelLog
{
    protected static function bootModelLog()
    {
        static::updating(function ($model) {
            foreach ((array) $model->log as $attribute) {
                if (in_array($attribute, array_keys($model->getDirty()))) {
                    ModelLogModel::create([
                        'attribute' => $attribute,
                        'from' => serialize($model->getOriginal($attribute)),
                        'model_foreign_key' => $model->id,
                        'model_name' => get_class($model),
                        'to' => serialize($model->$attribute),
                        'updated_by_user_id' => auth()->user()->id ?? null,
                    ]);
                }
            }
        });
    }

    public function getLogEntriesAttribute()
    {
        $output = collect();

        foreach (ModelLogModel::where('model_name', get_class($this))->get() as $item) {
            $item->from = $item->from;
            $item->to = $item->to;
            $output->push($item);
        }

        return $output;
    }
}
