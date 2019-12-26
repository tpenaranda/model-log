<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LogEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tpenaranda_model_log_log_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('model_foreign_key')->unsigned()->index();
            $table->string('model_name')->index();
            $table->string('attribute')->index();
            $table->longText('from');
            $table->longText('to');
            $table->string('updated_by_user_id')->nullable()->index();
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
        Schema::dropIfExists('tpenaranda_model_log_log_entries');
    }
}
