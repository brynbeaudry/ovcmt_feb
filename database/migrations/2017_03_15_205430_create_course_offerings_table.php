<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseOfferingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_offerings', function (Blueprint $table) {
            $table->increments('crn')->length(10)->unsigned();;
            $table->integer('term_id')->length(10)->unsigned();;
            $table->integer('instructor_id')->length(10)->unsigned();;
            $table->string('course_id');
            $table->char('intake_no');
            $table->integer('ta_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_offerings');
    }
}
