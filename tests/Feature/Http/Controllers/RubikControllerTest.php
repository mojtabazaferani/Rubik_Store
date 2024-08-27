<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RubikControllerTest extends TestCase
{
    public function test_view_register(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);

        $response->assertViewIs('register');
    }

    public function test_register_success(): void
    {
        $response1 = $this->post(route('store'), [
            'name' => 'ahmad',
            'email' => 'ahmad@gmail.com',
            'password' => '88889999'
        ]);

        $response1->assertRedirect(route('login'));

        $response2 = $this->post(route('check'), [
            'email' => 'ahmad@gmail.com',
            'password' => '88889999'
        ]);

        $response2->assertRedirect(route('profile'));
    }

    public function test_view_reset_password(): void
    {
        $response = $this->get(route('reset.password'));

        $response->assertStatus(200);

        $response->assertViewIs('reset-password');
    }

    public function test_update_password(): void
    {
        $response = $this->post(route('update.password'), [
            'email' => 'ahmad@gmail.com'
        ]);

        $response->assertRedirect('change.password');
    }
       
}
