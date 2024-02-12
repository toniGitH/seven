<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    public function testRankingExecutedByAuthenticatedAdmin()
    {
        $adminUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();

        $response = $this->actingAs($adminUser)->json('GET', 'api/players/ranking');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'players'
        ]);
    }

    public function testRankingExecutedByAuthenticatedAdminIfNotPlayersExisting()
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

    public function testRankingExecutedByAuthenticatedPlayer()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $response = $this->actingAs($playerUser)->json('GET', 'api/players/ranking');
        
        $response->assertStatus(403);
    }

    public function testRankingExecutedByUnauthenticatedUser()
    {
        $response = $this->json('GET', 'api/players/ranking');
        $response->assertStatus(401);
    }
}
