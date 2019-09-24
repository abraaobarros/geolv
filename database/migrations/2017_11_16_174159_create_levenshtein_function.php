<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLevenshteinFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $levenshtein = file_get_contents(storage_path('app/levenshtein.sql'));
        if (DB::getDefaultConnection() == 'mysql')
            DB::unprepared($levenshtein);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::getDefaultConnection() == 'mysql')
            DB::unprepared('DROP FUNCTION levenshtein;');
    }
}
