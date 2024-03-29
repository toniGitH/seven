<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class GetWinRateTest extends TestCase
{
    use RefreshDatabase;

    public function testGetWinRateToAnAuthenticatedPlayerWhoHasRolls()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $response = $this->actingAs($playerUser)->json('GET', '/api/players/' . ($playerUser->id) . '/average');

        $response->assertStatus(200);
        $response->assertJsonStructure([
                    'user',
                    'current success rate'
        ]);
    }

    public function testGetWinRateToAnAuthenticatedPlayerWhoHasNoRolls()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $playerUser->rolls()->delete();

        $response = $this->actingAs($playerUser)->json('GET', '/api/players/' . ($playerUser->id) . '/games');

        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'No rolls found for the user'
        ]);
    }

    public function testGetAnotherPlayerWinRateToAnAuthenticatedPlayer()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $response = $this->actingAs($playerUser)->json('GET', '/api/players/' . ($playerUser->id)+1 . '/average');

        $response->assertStatus(403);         
    }

    public function testGetWinRateToAnAuthenticatedAdmin()
    {
        $adminUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();

        $response = $this->actingAs($adminUser)->json('GET', '/api/players/' . $adminUser->id . '/average');

        $response->assertStatus(403);
    }

    public function testGetWinRateToAnUnauthenticatedUser()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $response = $this->json('GET', '/api/players/' . $playerUser->id . '/average');

        $response->assertStatus(401);  
    }
}

