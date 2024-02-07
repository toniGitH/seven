<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\ClientRepository;

class UserUpdateTest extends TestCase
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

    public function testAuthenticatedUserUpdatesWithSpecifiedName()
    {
        $user = User::factory()->create([
            'name' => 'testname'
        ]);

        $this->actingAs($user, 'api');

        $response = $this->json('PUT', '/api/players/' . $user->id, ['name' => 'newtestname']);

        $updatedUser = User::find($user->id);
        $this->assertEquals('newtestname', $updatedUser->name);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Username updated successfully',
            'new name' => 'newtestname'
        ]);
    }

    public function testAuthenticatedUserUpdatesWithoutSpecifiedName()
    {
        $user = User::factory()->create([
            'name' => 'testname'
        ]);

        $this->actingAs($user, 'api');

        $response = $this->json('PUT', '/api/players/' . $user->id, ['name' => '']);

        $updatedUser = User::find($user->id);
        $this->assertEquals('anonimo', $updatedUser->name);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Username updated successfully',
            'new name' => 'anonimo'
        ]);
    }

    public function testAuthenticatedUserAttemptingToUpdateNotHimself()
    {
        $user = User::factory()->create([
            'name' => 'testname'
        ]);

        $notMyId = $user->id + 1;

        $this->actingAs($user, 'api');

        $response = $this->json('PUT', '/api/players/' . $notMyId, ['name' => 'newname']);

        $response->assertStatus(403);

        $updatedUser = User::find($user->id);
        $this->assertEquals($user->name, $updatedUser->name);
    }

    public function testUnauthenticatedUserTriesToUpdate()
    {
        $user = User::factory()->create([
            'name' => 'testname'
        ]);
        
        $response = $this->json('PUT', '/api/players/' . $user->id, ['name' => 'newtestname']);
    
        $updatedUser = User::find($user->id);
        $this->assertEquals('testname', $updatedUser->name);
    
        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ]);
    }
    
}
