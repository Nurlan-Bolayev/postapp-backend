<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_register()
    {
        $password = 'nurlan12';
        $email = 'bolayev.nurlan@gmail.com';
        $data = [
            'name' => 'Nurlan',
            'email' => $email,
            'password' => $password,
        ];

        $this
            ->postJson('api/register', $data)
            ->dump()
            ->assertJson([
                'email' => $email,
            ]);
        $this
            ->assertDatabaseHas('users', [
                'email' => $email,
            ]);
    }

    public function test_user_can_login()
    {
        $email = 'bolayev.nurlan@gmail.com';
        $password = 'nurlan12';

        $user = User::factory()->create([
            'email' => $email,
            'password' => \Hash::make($password),
        ]);
        $this
            ->actingAs($user)
            ->postJson('api/login', [
                'email' => $email,
                'password' => $password,
            ])
            ->assertJson([
                'email' => $email,
            ]);
        $this
            ->assertDatabaseHas('users', [
                'email' => $email,
            ]);
    }


    public function test_user_fails_register()
    {
        $body = [
            'name' => 'Nurlan',
            'email' => '',
            'password' => 'nur',

        ];
        $this
            ->postJson('api/register', $body)
            ->assertJson([
                'errors' => [
                    'email' => ['The email field is required'],
                    'password' => ['The password is too short'],
                ]]);
        $this
            ->assertDatabaseMissing('users', [
                'email' => 'bolayev.nurlan@gmail.com',
            ]);
    }

    public function test_user_fails_login()
    {
        $body = [
            $email = 'bolayev.nurlan@gmail.com',
            $password = 'nur12',
        ];

        $user = User::factory()->create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);
        $this
            ->actingAs($user)
            ->postJson('api/login', [
                'email' => $email,
                'password' => 'nurlan',
            ])
            ->assertJson([
                'errors' => [
                    'password' => ['The password is incorrect.']
                ]
            ]);
    }
}
