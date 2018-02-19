<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfoNews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() 
    {
		/*
			If you recieve QueryException SQLSTATE[42S22], set timestamps = false like below
		*/
		$timestamps = false;
        Schema::create('info_news', function (Blueprint $table) {
            $table->increments('news_id')->length(11);
			$table->binary('news_blob');
            $table->string('news_title');
            $table->string('news_link');
            $table->string('news_image');
            $table->text('news_full_content');
            $table->string('news_author');
            $table->string('news_publish_date');
			$table->primary(['news_id']);
			
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('info_news');
    }
}
