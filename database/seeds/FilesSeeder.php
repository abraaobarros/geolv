<?php

use GeoLV\GeocodingFile;
use GeoLV\User;
use Illuminate\Database\Seeder;

class FilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if ($this->command->confirm('Seed fake files?')) {
            GeocodingFile::truncate();

            foreach (User::all() as $user) {
                factory(GeocodingFile::class)->times(rand(1, 10))->create([
                    'user_id' => $user->id
                ]);
            }
        }
    }
}
