<?php

use GeoLV\GeocodingFile;
use GeoLV\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(LocalitySeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(FilesSeeder::class);
    }
}
