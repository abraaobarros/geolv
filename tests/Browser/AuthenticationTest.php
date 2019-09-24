<?php

namespace Tests\Feature;

use GeoLV\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AuthenticationTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testLogin()
    {
        $user = factory(User::class)->create();

        Notification::fake();

        $this->browse(function (Browser $browser) use ($user) {

            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'secret')
                ->click('button[type=submit]')
                ->assertPathIs('/email/verify');

        });

        Notification::assertSentTo(
            [$user], VerifyEmail::class
        );
    }
}
