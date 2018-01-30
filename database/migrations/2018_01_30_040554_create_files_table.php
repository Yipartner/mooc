<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('file_id');
            $table->string('file_url');
            $table->string('file_name');
            $table->integer('lesson_id');
            $table->string('status_id')->nullable()->default(null);
            //0成功，1等待处理，2正在处理，3处理失败，4通知提交失败。
            $table->string('status')->default(1);
            $table->string('true_file_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
