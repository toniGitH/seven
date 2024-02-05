<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        $this->createPersonalAccessTokenClient();
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

    public function testRegisterWithoutData()
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

    protected function createPersonalAccessTokenClient()
    {
        $clientRepository = app(ClientRepository::class);
        $clientRepository->createPersonalAccessClient(
            null, // No se requiere el usuario
            'Personal Access Client', // Nombre del cliente
            'http://localhost' // Redirección del cliente
        );
    }
}
