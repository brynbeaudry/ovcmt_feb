<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubstitutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('substitutions', function (Blueprint $table) {
            $table->increments('id');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('sub_crn')->length(10)->unsigned();
            $table->integer('sub_instructor_id')->length(10)->unsigned();
            $table->integer('sub_ta_id')->nullable();
            $table->foreign('sub_crn')
                ->references('crn')
                ->on('course_offerings');
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
        Schema::dropIfExists('substitutions');
    }
}
