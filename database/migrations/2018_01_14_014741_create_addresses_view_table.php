<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateAddressesViewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            CREATE VIEW addresses_view AS 
              SELECT 
                a.*,
                s.id AS search_id,
                s.text AS search_text,
                s.postal_code AS search_postal_code,
                s.locality AS search_locality
              FROM address_search AS ads
              JOIN addresses AS a ON a.id = ads.address_id
              JOIN searches AS s ON s.id = ads.search_id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW addresses_view');
    }
}
