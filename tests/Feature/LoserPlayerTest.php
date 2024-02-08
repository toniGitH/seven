<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\ClientRepository;
use App\Models\User;
use Tests\TestCase;

class LoserPlayerTest extends TestCase
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

    public function testLoserPlayerExecutedByAuthenticatedAdmin()
    {
        $adminUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();

        $response = $this->actingAs($adminUser)->json('GET', 'api/players/ranking/loser');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'loser'
        ]);
    }

    public function testLoserPlayerExecutedByAuthenticatedAdminIfNotPlayersExisting()
    {
        $adminUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();

        $players = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->get();
    
        foreach ($players as $player) {
            $player->rolls()->delete();
        }

        User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->delete();

        $response = $this->actingAs($adminUser)->json('GET', '/api/players');
        
        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'No players found yet.',
            'players' => []
        ]);
    }

    public function testLoserPlayerExecutedByAuthenticatedPlayer()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $response = $this->actingAs($playerUser)->json('GET', 'api/players/ranking/loser');
        
        $response->assertStatus(403);
    }

    public function testLoserPlayerExecutedByUnauthenticatedUser()
    {
        $response = $this->json('GET', 'api/players/ranking/loser');
        $response->assertStatus(401);
    }
}
