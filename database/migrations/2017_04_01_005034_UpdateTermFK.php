<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTermFK extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('terms', function (Blueprint $table) {
            $table->foreign('intake_id')->references('intake_id')
                ->on('intakes')
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
        Schema::table('terms', function (Blueprint $table) {
            $table->dropForeign(['intake_id']);
        });
    }
}
