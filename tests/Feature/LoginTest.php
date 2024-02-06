<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\ClientRepository;
use App\Models\User;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
        $this->createPersonalAccessTokenClient();
        $this->seed();
    }

    protected function createPersonalAccessTokenClient()
    {
        $clientRepository = app(ClientRepository::class);
        $clientRepository->createPersonalAccessClient(
            null,
            'Personal Access Client',
            'http://localhost'
        );
    }
    
    public function testLoginWithValidCredentials()
    {
        $user = User::factory()->create([
            'email' => 'testname@email.com',
            'password' => '12345678'
        ]);

        $response = $this->json('POST','/api/login', [
            'email' => 'testname@email.com',
            'password' => '12345678',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'token',
            'user' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
        ]);
        // token check
        $responseData = $response->json();
        $token = $responseData['token'];
        $this->assertNotNull($token);
        $this->assertNotEmpty($token);

        $this->assertEquals('User ' . ucfirst($user->name) . ' logged successfully', $response->json()['message']);
    }
 
    public function testLoginWithoutCredentials()
    {
        $response = $this->json('POST','/api/login', []);
        $response->assertStatus(422);
        $response->assertJson([
                'message' => 'The email field is required. (and 1 more error)',
                'errors' => [
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.'],
                ],
        ]);
    }

    public function testLoginWithoutEmail()
    {
        $response = $this->json('POST','/api/login', [
            'password' => '12345678',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
                     'message' => 'The email field is required.',
                     'errors' => [
                         'email' => ['The email field is required.'],
                     ],
                 ]);
    }

    public function testRegisterWithInvalidEmailFormat()
    {
        $response = $this->json('POST','/api/login', [
            'email' => 'testnameemail.com',
            'password' => '12345678',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
                     'message' => 'The email field must be a valid email address.',
                     'errors' => [
                         'email' => ['The email field must be a valid email address.'],
                     ],
                 ]);
    } // This test fails if an email without a dot is considered incorrect. Possible bug in Laravel.

    public function testLoginWithUnregisteredEmail()
    {
        $user = User::factory()->create([
            'email' => 'testname@email.com',
            'password' => '12345678'
        ]);

        $response = $this->json('POST','/api/login', [
            'email' => 'unregisteredemail@email.com',
            'password' => '12345678',
        ]);

        $this->assertNotEquals($user->email, 'unregisteredemail@email.com');
        $response->assertStatus(422)
                ->assertJson([
                    'message' => 'The provided credentials are incorrect.',
        ]);
    }

    public function testLoginWithoutPassword()
    {
        $response = $this->json('POST','/api/login', [
            'email' => 'testname@email.com',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
                     'message' => 'The password field is required.',
                     'errors' => [
                         'password' => ['The password field is required.'],
                     ],
                 ]);
    }

    public function testLoginWithUnregisteredPassword()
    {
    
        $user = User::factory()->create([
            'email' => 'testname@email.com',
            'password' => '12345678'
        ]);

        $response = $this->json('POST','/api/login', [
            'email' => 'testname@email.com',
            'password' => 'unregisteredPassword',
        ]);

        $this->assertNotEquals($user->password, 'unregisteredPassword');
        $response->assertStatus(422)
                ->assertJson([
                    'message' => 'The provided credentials are incorrect.',
        ]);
    }

}