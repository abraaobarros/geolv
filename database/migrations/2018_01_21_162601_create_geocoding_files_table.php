<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeocodingFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geocoding_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('path');
            $table->integer('offset')->unsigned()->default(0);
            $table->integer('count')->unsigned()->default(1);
            $table->boolean('done')->default(false);
            $table->boolean('header')->default(false);
            $table->char('delimiter')->default(',');
            $table->text('indexes');
            $table->text('fields');
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geocoding_files');
    }
}
