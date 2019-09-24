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
        if (DB::getDefaultConnection() == 'mysql')
            DB::statement("
                CREATE OR REPLACE VIEW addresses_view AS 
                  SELECT 
                    a.*,
                    s.id AS search_id,
                    s.text AS search_text,
                    s.postal_code AS search_postal_code,
                    TRIM(SUBSTRING_INDEX(s.locality, '-', 1)) AS search_locality,
                    IF(TRIM(SUBSTRING_INDEX(s.locality, '-', -1)) = TRIM(SUBSTRING_INDEX(s.locality, '-', 1)), '', TRIM(SUBSTRING_INDEX(s.locality, '-', -1))) AS search_state
                  FROM address_search AS ads
                  JOIN addresses AS a ON a.id = ads.address_id
                  JOIN searches AS s ON s.id = ads.search_id");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::getDefaultConnection() == 'mysql')
            DB::statement('DROP VIEW addresses_view');
    }
}
