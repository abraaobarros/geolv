<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLevenshteinFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $levenshtein = file_get_contents(storage_path('app/levenshtein.sql'));
        \DB::unprepared($levenshtein);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::unprepared('DROP IF EXISTS FUNCTION levenshtein;');
    }
}
