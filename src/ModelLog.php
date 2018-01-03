<?php

namespace TPenaranda\ModelLog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

class ModelLog
{
    public static function getLogTableName(Model $model)
    {
        return $model::getModel()->getTable() . '_log';
    }

    public static function getLogTableSchema(Model $model) {
        $model_foreign_key = $model::getModel()->getForeignKey();
        $model_table_name = $model::getModel()->getTable();

        return function (Blueprint $table) use ($model_foreign_key, $model_table_name) {
            $table->increments('id');
            $table->integer($model_foreign_key)->unsigned()->index();
            $table->string('attribute')->index();
            $table->string('from');
            $table->string('to');
            $table->integer('updated_by_user_id')->nullable()->unsigned()->index();
            $table->foreign($model_foreign_key)->references('id')->on($model_table_name);
            $table->foreign('updated_by_user_id')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        };
    }
}
