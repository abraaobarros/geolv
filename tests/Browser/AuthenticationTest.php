<?php

namespace Tests\Browser;

use GeoLV\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AuthenticationTest extends DuskTestCase
{
    use WithFaker;

    public function testLogin()
    {
        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {

            $browser->visit('/')
                ->type('email', $user->email)
                ->type('password', 'secret')
                ->click('button[type=submit]')
                ->assertPathIs('/home')
                ->assertAuthenticatedAs($user)
                ->logout();

            $user->forceDelete();

        });
    }

    public function testNotGoogleApiKeyRegister()
    {
        $this->browse(function (Browser $browser) {

            $browser
                ->assertGuest()
                ->visit('/register')
                ->type('email', $this->faker->email)
                ->type('password', 'secret')
                ->type('password_confirmation', 'secret')
                ->type('name', $this->faker->name)
                ->click('button[type=submit]')
                ->assertSee('O campo Google Maps API Key é obrigatório');

        });

    }

    public function testRegister()
    {
        $this->browse(function (Browser $browser) {

            $email = $this->faker->email;

            $browser
                ->visit('/register')
                ->type('email', $email)
                ->type('password', 'secret')
                ->type('password_confirmation', 'secret')
                ->type('name', $this->faker->name)
                ->type('google_maps[api_key]', $this->faker->text(100))
                ->click('button[type=submit]')
                ->assertPathIs('/email/verify')
                ->assertAuthenticated()
                ->logout();

        });

    }
}
