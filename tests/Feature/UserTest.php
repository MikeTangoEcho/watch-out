<?php

namespace Tests\Feature;

use App;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Guest access on all resources
     *
     * @return void
     */
    public function testGuestAccess()
    {
        $this->get('/')
            ->assertOk();

        $this->get('/screen')
            ->assertOk();

        $this->get('/streams')
            ->assertOk();
 
        $this->get('/record')
            ->assertRedirect('/login');

        $this->get('/users')
            ->assertRedirect('/login');

        $this->get('/history')
            ->assertRedirect('/login');
    }

    /**
     * Test Auth user with/out email verified access on all resources
     * @return void
     */
    public function testAuthAccess()
    {
        $user = factory(App\User::class)->create([
            'email_verified_at' => null
        ]);

        $this->actingAs($user)
            ->get('/')
            ->assertOk();

        $this->actingAs($user)
            ->get('/record')
            ->assertRedirect('/email/verify');

        $this->actingAs($user)
            ->get('/users')
            ->assertOk();

        $this->actingAs($user)
            ->get('/history')
            ->assertRedirect('/email/verify');

        $this->actingAs($user)
            ->get('/screen')
            ->assertOk();

        $this->actingAs($user)
            ->get('/streams')
            ->assertOk();

        $user->email_verified_at = now();
        $user->save();

        $this->actingAs($user)
            ->get('/record')
            ->assertOk();

        $this->actingAs($user)
            ->get('/history')
            ->assertOk();
    }

    /**
     * Test User's Name update and password
     */
    public function testUserEdit()
    {
        $user = factory(App\User::class)->create();

        $this->actingAs($user)
            ->put('/users/' . $user->id, ['name' => 'Hello'])
            ->assertRedirect('/users/' . $user->id . '/edit');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Hello']);
    }

}
