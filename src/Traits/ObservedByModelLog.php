<?php

namespace TPenaranda\ModelLog\Traits;

use TPenaranda\ModelLog\ModelLogEntry;
use ReflectionClass;

trait ObservedByModelLog
{
    protected static function bootObservedByModelLog()
    {
        static::updating(function ($model) {
            foreach (array_keys($model->getDirty()) as $attribute) {
                if (('all' === $model->log) || in_array($attribute, (array) $model->log)) {
                    ModelLogEntry::create([
                        'attribute' => $attribute,
                        'from' => serialize($model->getOriginal($attribute)),
                        'model_foreign_key' => $model->id,
                        'model_name' => get_class($model),
                        'to' => serialize($model->$attribute),
                        'updated_by_user_id' => auth()->user() ? auth()->user()->id : null,
                    ]);
                }
            }
        });
    }

    public function getLogEntriesAttribute()
    {
        $output = collect();

        foreach (ModelLogEntry::where('model_name', get_class($this))->where('model_foreign_key', $this->id)->get() as $item) {
            $item->from = $item->from;
            $item->to = $item->to;
            $output->push($item);
        }

        return $output;
    }
}
