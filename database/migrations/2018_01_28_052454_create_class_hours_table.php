<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_hours', function (Blueprint $table) {
            $table->increments('class_hour_id');
            $table->integer('lesson_id');
            //1:视频 2:文本
            $table->integer('class_type');
            //0:不免费 1:免费
            $table->integer('free');
            $table->string('class_url')->nullable();
            $table->text('class_content')->nullable();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_hours');
    }
}
