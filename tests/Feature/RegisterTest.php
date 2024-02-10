<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
        $clientRepository = app(ClientRepository::class);
        $clientRepository->createPersonalAccessClient(
            null,
            'Personal Access Client',
            'http://localhost'
        );
        $this->seed();
    }

    public function testRegisterWithValidData()
    {
        $userData = [
            'name' => 'testname',
            'email' => 'testname@email.com',
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ];
        $response = $this->json('POST', '/api/players', $userData);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'user' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at'
            ],
            'token'
        ]);
        $response->assertJsonFragment([
            'name' => 'testname',
            'email' => 'testname@email.com' 
        ]);
        // role assignment check
        $user = User::where('email', 'testname@email.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('player'));
        // token check
        $responseData = $response->json();
        $token = $responseData['token'];
        $this->assertNotNull($token);
        $this->assertNotEmpty($token);
    }

    public function testRegisterWithoutData()
    {
        $userData = [];

        $response = $this->json('POST', '/api/players', $userData);
        $response->assertStatus(422);
        $response->assertJson([
            "message" => "The email field is required. (and 1 more error)",
            "errors" => [
                "email" => ["The email field is required."],
                "password" => ["The password field is required."]
            ]
        ]);
    }

    public function testRegisterWithMissingName()
    {
        $userData = [
           //'name' => 'testname',
           'email' => 'testname@email.com',
           'password' => '12345678',
           'password_confirmation' => '12345678'
        ];
        $response = $this->json('POST', '/api/players', $userData);
        $response->assertStatus(201);
        $response->assertJsonStructure([
           'message',
           'user' => [
               'id',
               'name',
               'email',
               'created_at',
               'updated_at'
            ],
           'token'
        ]);
        $response->assertJsonFragment([
            'name' => 'anonimo',
            'email' => 'testname@email.com' 
        ]);
        // role assignment check
        $user = User::where('email', 'testname@email.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('player'));
        // token check
        $responseData = $response->json();
        $token = $responseData['token'];
        $this->assertNotNull($token);
        $this->assertNotEmpty($token);
    }
 
    public function testRegisterWithMissingEmail()
    {
        $userData = [
            'name' => 'testname',
            //'email' => 'testname@email.com',
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ];

        $response = $this->json('POST', '/api/players', $userData);
        $response->assertStatus(422);
        $response->assertJson([
            "message" => "The email field is required.",
            "errors" => [
                "email" => ["The email field is required."]
            ]
        ]);
    }

    public function testRegisterWithInvalidEmailFormat()
    {
        $userData = [
            'name' => 'testname',
            'email' => 'testnameemail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ];

        $response = $this->json('POST', '/api/players', $userData);
        $response->assertStatus(422);
        $response->assertJson([
            "message" => "The email field must be a valid email address.",
            "errors" => [
                "email" => ["The email field must be a valid email address."]
            ]
        ]);
    } // This test fails if an email without a dot is considered incorrect. Possible bug in Laravel.

    public function testRegisterWithMissingPassword()
    {
        $userData = [
            'name' => 'testname',
            'email' => 'testname@email.com',
            //'password' => '12345678',
            //'password_confirmation' => '12345678'
        ];

        $response = $this->json('POST', '/api/players', $userData);
        $response->assertStatus(422);
        $response->assertJson([
            "message" => "The password field is required.",
            "errors" => [
                "password" => ["The password field is required."]
            ]
        ]);
    }

    public function testRegisterWithMissingPasswordConfirmation()
    {
        $userData = [
            'name' => 'testname',
            'email' => 'testname@email.com',
            'password' => '12345678',
            //'password_confirmation' => '12345678'
        ];

        $response = $this->json('POST', '/api/players', $userData);
        $response->assertStatus(422);
        $response->assertJson([
            "message" => "The password field confirmation does not match.",
            "errors" => [
                "password" => ["The password field confirmation does not match."]
            ]
        ]);
    }

}
