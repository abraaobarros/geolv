<?php

use Carbon\Carbon;
use GeoLV\GeocodingFile;
use GeoLV\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dev = new User();
        $dev->name = "Luiz Gabriel";
        $dev->email = "luizgabriel.info@gmail.com";
        $dev->password = bcrypt("123456");
        $dev->email_verified_at = Carbon::now();
        $dev->role = "dev";
        $dev->save();

        if ($this->command->confirm('Seed fake users?'))
            factory(User::class)->times(10)->create();
    }
}
