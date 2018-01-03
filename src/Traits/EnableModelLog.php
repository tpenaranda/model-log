<?php

namespace TPenaranda\ModelLog\Traits;

use TPenaranda\ModelLog\ModelLog;
use ReflectionClass;

trait EnableModelLog
{
    protected static function bootEnableModelLog()
    {
        $model = self::getModel();
        $table_name = ModelLog::getLogTableName($model);
        $model_name = (new ReflectionClass($model))->getShortName();
        $class_name = "{$model_name}LogEntry";
        $namespace = trim(str_before(self::class, $model_name), '\\');
        eval("
            namespace {$namespace};

            use Illuminate\Database\Eloquent\{Model, SoftDeletes};

            class {$class_name} extends Model {
                use SoftDeletes;

                protected \$table = '{$table_name}';
                protected \$dates = [
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ];
                protected \$guarded = [
                    'id',
                    'created_at',
                    'deleted_at',
                    'updated_at',
                ];

                public function getFromAttribute()
                {
                    return is_string(\$this->attributes['from']) ? unserialize(\$this->attributes['from']) : null;
                }

                public function getToAttribute()
                {
                    return is_string(\$this->attributes['to']) ? unserialize(\$this->attributes['to']) : null;
                }
            }
        ");

        static::updating(function ($model) {
            foreach ((array) $model->log as $attribute) {
                if (in_array($attribute, array_keys($model->getDirty()))) {
                    $model->logEntries()->create([
                        'attribute' => $attribute,
                        'from' => serialize($model->getOriginal($attribute)),
                        'to' => serialize($model->$attribute),
                        'updated_by_user_id' => auth()->user()->id ?? null,
                    ]);
                }
            }
        });
    }

    public function logEntries()
    {
        return $this->hasMany(self::class . 'LogEntry');
    }

    public function getLogEntriesAttribute()
    {
        $output = collect();

        foreach ($this->logEntries()->get() as $entry) {
            $entry->from = $entry->from;
            $entry->to = $entry->to;
            $output->push($entry);
        }

        return $output;
    }
}
