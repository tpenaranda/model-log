<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTPenarandaModelLogLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tpenaranda_model_log_logs_table', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('model_foreign_key')->unsigned()->index();
            $table->string('model_name')->index();
            $table->string('attribute')->index();
            $table->string('from');
            $table->string('to');
            $table->integer('updated_by_user_id')->nullable()->unsigned()->index();
            $table->foreign('updated_by_user_id')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tpenaranda_model_log_logs_table');
    }
}
