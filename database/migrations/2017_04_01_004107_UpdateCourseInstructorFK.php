<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCourseInstructorFK extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('course_instructors', function (Blueprint $table) {
            $table->foreign('instructor_id')->references('instructor_id')
                ->on('instructors')
                ->onDelete('cascade');

        });
        Schema::table('course_instructors', function (Blueprint $table) {
            $table->foreign('course_id')->references('course_id')
                ->on('courses')
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
        /*
        Schema::table('course_instructors', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
        });
        Schema::table('course_instructors', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
        });*/

    }
}
