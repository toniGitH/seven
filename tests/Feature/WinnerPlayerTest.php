<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\ClientRepository;
use App\Models\User;
use Tests\TestCase;

class WinnerPlayerTest extends TestCase
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

    public function testWinnerPlayerExecutedByAuthenticatedAdmin()
    {
        $adminUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();

        $response = $this->actingAs($adminUser)->json('GET', 'api/players/ranking/winner');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'winner'
        ]);
    }

    public function testWinnerPlayerExecutedByAuthenticatedAdminIfNotPlayersExisting()
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

    public function testWinnerPlayerExecutedByAuthenticatedPlayer()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $response = $this->actingAs($playerUser)->json('GET', 'api/players/ranking/winner');
        
        $response->assertStatus(403);
    }

    public function testWinnerPlayerExecutedByUnauthenticatedUser()
    {
        $response = $this->json('GET', 'api/players/ranking/winner');
        $response->assertStatus(401);
    }
}
