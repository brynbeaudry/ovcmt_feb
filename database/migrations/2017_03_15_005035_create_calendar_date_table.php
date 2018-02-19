<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarDateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_dates', function (Blueprint $table) {
            $table->date('cdate');
            $table->tinyInteger('cdayOfMonth');
            $table->tinyInteger('cmonth');
            $table->smallInteger('cyear');
            $table->tinyInteger('cdayOfWeek');
            $table->tinyInteger('cweek');
            $table->boolean('isWeekday');
            $table->boolean('isHoliday');
            $table->string('holidayDesc');
            $table->primary('cdate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendar_dates');
    }
}
