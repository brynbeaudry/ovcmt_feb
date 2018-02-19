<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCourseOfferingsFK extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->foreign(['instructor_id', 'course_id', 'intake_no'])
                ->references(['instructor_id', 'course_id', 'intake_no'])
                ->on('course_instructors')
                ->onDelete('cascade');
        });
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->foreign('term_id')
                ->references('term_id')
                ->on('terms')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->dropForeign(['instructor_id', 'course_id', 'intake_no']);
        });
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->dropForeign(['term_id']);
        });
    }
}
