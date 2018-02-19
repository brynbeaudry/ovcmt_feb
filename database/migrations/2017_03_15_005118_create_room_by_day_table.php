<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomByDayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms_by_days', function (Blueprint $table) {
            $table->string('room_id')->length(2);
            $table->date('cdate');
            $table->integer('am_crn')->nullable()->length(10)->unsigned();;
            $table->integer('pm_crn')->nullable()->length(10)->unsigned();;
            $table->primary(['room_id','cdate']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms_by_days');
    }
}
