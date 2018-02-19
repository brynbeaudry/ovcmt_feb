<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstructAvailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instruct_avails', function (Blueprint $table) {
            $table->integer('instructor_id')->length(10)->unsigned();;
            $table->date('date_start');
            $table->boolean('mon_am');
            $table->boolean('mon_pm');

            $table->boolean('tues_am');
            $table->boolean('tues_pm');

            $table->boolean('wed_am');
            $table->boolean('wed_pm');

            $table->boolean('thurs_am');
            $table->boolean('thurs_pm');

            $table->boolean('fri_am');
            $table->boolean('fri_pm');
            $table->primary(['instructor_id', 'date_start']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('instruct_avails');
    }
}
