<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\ClientRepository;

class LogoutTest extends TestCase
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
    
    public function testAuthenticatedLogout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
 
        $response = $this->json('POST', 'api/logout');
 
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'User ' . ucfirst($user->name) . ' logged out successfully'
        ]);
    }

    public function testUnauthenticatedLogout(){
        $response = $this->json('POST','api/logout');
        $response->assertStatus(401);
    }
}
