<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->increments('term_id')->length(10)->unsigned();;
            $table->date('term_start_date');
            $table->integer('intake_id')->length(10)->unsigned();;
            $table->tinyInteger('term_no');
            $table->tinyInteger('duration_weeks')->default(0);
            $table->tinyInteger('course_weeks')->default(0);
            $table->tinyInteger('break_weeks')->default(0);
            $table->tinyInteger('exam_weeks')->default(0);
            $table->tinyInteger('holidays')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('terms');
    }
}
