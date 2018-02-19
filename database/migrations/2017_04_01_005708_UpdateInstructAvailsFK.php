<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInstructAvailsFK extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('instruct_avails', function (Blueprint $table) {
            $table->foreign('instructor_id')->references('instructor_id')
                ->on('instructors')
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
        Schema::table('instruct_avails', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
        });
    }
}
