<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\ClientRepository;
use App\Models\User;
use App\Models\Roll;
use Tests\TestCase;

class ShowRollsTest extends TestCase
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

    public function testShowRollsListToAnAuthenticatedPlayer()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $response = $this->actingAs($playerUser)->json('GET', '/api/players/' . ($playerUser->id) . '/games');

        $response->assertStatus(200);
        $response->assertJsonStructure([
                    'message',
                    'current success rate',
                    'rolls'
        ]);
    }

    public function testShowAnotherPlayerRollsListToAnAuthenticatedPlayer()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $response = $this->actingAs($playerUser)->json('GET', '/api/players/' . ($playerUser->id)+1 . '/games');

        $response->assertStatus(403);
                
    }

    public function testShowRollsListToAuthenticatedAdmin()
    {
        $adminUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();

        $response = $this->actingAs($adminUser)->json('GET', '/api/players/' . $adminUser->id . '/games');

        $response->assertStatus(403);
    }

    public function testShowRollsListToUnauthenticatedUser()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $response = $this->json('GET', '/api/players/' . $playerUser->id . '/games');

        $response->assertStatus(401);  
    }
}
