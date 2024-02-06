<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\ClientRepository;
use App\Models\User;
use App\Models\Roll;
use Tests\TestCase;

class StoreRollTest extends TestCase
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

    public function testRollExecutedByAuthenticatedPlayer()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $response = $this->actingAs($playerUser)->json('POST', '/api/players/' . $playerUser->id . '/games');

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'roll result' => [
                         'id',
                         'dice1',
                         'dice2',
                         'won',
                         'user_id',
                         'created_at',
                         'updated_at'
                     ]
                 ]);

        $roll = Roll::latest()->first();

        $this->assertEquals($playerUser->id, $roll->user_id);
    }

    public function testRollExecutedByAuthenticatedPlayerTryingToAssignRollToAnotherPlayer()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $response = $this->actingAs($playerUser)->json('POST', '/api/players/' . ($playerUser->id)+1 . '/games');

        $response->assertStatus(403);        
    }

    public function testRollExecutedByAuthenticatedAdmin()
    {

        $adminUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();

        $response = $this->actingAs($adminUser)->json('POST', '/api/players/' . $adminUser->id . '/games');

        $response->assertStatus(403);
    }

    public function testRollExecutedByUnauthenticatedUser()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $response = $this->json('POST', '/api/players/' . $playerUser->id . '/games');

        $response->assertStatus(401);  
    }
}
