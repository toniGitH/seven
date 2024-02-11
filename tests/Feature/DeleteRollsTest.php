<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\ClientRepository;
use App\Models\User;
use App\Models\Roll;
use Tests\TestCase;

class DeleteRollsTest extends TestCase
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

    public function testDeleteRollsByAuthenticatedPlayerWhoHasRolls()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $response = $this->actingAs($playerUser)->json('DELETE', '/api/players/' . ($playerUser->id) . '/games');

        $response->assertStatus(200);

        $response->assertJsonStructure([
                    'message',
        ]);

        $response->assertJson([
            'message' => 'All ' . ucfirst($playerUser->name) . '\'s rolls deleted successfully'
        ]);

        $rollsAfterDelete = Roll::where('user_id', $playerUser->id)->count();
        $this->assertEquals(0, $rollsAfterDelete);
    }

    public function testDeleteRollsByAuthenticatedPlayerWhoHasNoRolls()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $playerUser->rolls()->delete();

        $response = $this->actingAs($playerUser)->json('DELETE', '/api/players/' . ($playerUser->id) . '/games');

        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'No rolls found for the user'
        ]);
    }

    public function testDeleteAnotherPlayerRollsByAuthenticatedPlayer()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $response = $this->actingAs($playerUser)->json('DELETE', '/api/players/' . ($playerUser->id)+1 . '/games');

        $response->assertStatus(403);
    }

    public function testDeleteRollsByAuthenticatedAdmin()
    {
        $adminUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();

        $response = $this->actingAs($adminUser)->json('DELETE', '/api/players/' . $adminUser->id . '/games');

        $response->assertStatus(403);
    }

    public function testGetWinRateToAnUnauthenticatedUser()
    {
        $playerUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->first();

        $response = $this->json('GET', '/api/players/' . $playerUser->id . '/games');

        $response->assertStatus(401);  
    }
}


