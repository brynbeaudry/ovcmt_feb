<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCoursesByDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('rooms_by_days', function (Blueprint $table) {
            $table->unsignedInteger('am_sub')->nullable();
            $table->unsignedInteger('pm_sub')->nullable();
            $table->foreign('am_sub')
              ->references('id')
              ->on('substitutions')
              ->onDelete('cascade');
            $table->foreign('pm_sub')
              ->references('id')
              ->on('substitutions')
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
        Schema::table('rooms_by_days', function (Blueprint $table) {
            $table->dropForeign(['pm_sub','am_sub']);
            $table->dropColumn('am_sub');
            $table->dropColumn('pm_sub');
        });
    }
}
