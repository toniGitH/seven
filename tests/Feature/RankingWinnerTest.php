<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\ClientRepository;
use App\Models\User;
use Tests\TestCase;

class RankingWinnerTest extends TestCase
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

    public function testRankingWinnerExecutedByAuthenticatedAdmin()
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

    public function testRankingWinnerExecutedByAuthenticatedPlayer()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $response = $this->actingAs($playerUser)->json('GET', 'api/players/ranking/winner');
        
        $response->assertStatus(403);
    }

    public function testRankingExecutedByUnauthenticatedUser()
    {
        $response = $this->json('GET', 'api/players/ranking/winner');
        $response->assertStatus(401);
    }
}
