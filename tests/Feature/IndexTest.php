<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\ClientRepository;
use App\Models\User;
use App\Models\Roll;
use Tests\TestCase;

class IndexTest extends TestCase
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

    public function testIndexExecutedByAuthenticatedAdmin()
    {
        $adminUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();

        $response = $this->actingAs($adminUser)->json('GET', '/api/players');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'players' => [
                '*' => [
                    'user',
                    'win_rate',
                ],
            ],
        ]);
    }

    public function testIndexExecutedByAuthenticatedAdminIfNotPlayersExisting()
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

    public function testIndexExecutedByAuthenticatedPlayer()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $response = $this->actingAs($playerUser)->json('GET', '/api/players');
        
        $response->assertStatus(403);
    }

    public function testIndexExecutedByUnauthenticatedUser()
    {
        $response = $this->json('GET', '/api/players');
        $response->assertStatus(401);
    }

}


