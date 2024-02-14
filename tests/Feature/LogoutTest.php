<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function testAuthenticatedLogout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
 
        $response = $this->json('POST', 'api/logout');
 
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'User ' . ucfirst($user->name) . ' logged out successfully'
        ]);
    }

    public function testUnauthenticatedLogout(){
        $response = $this->json('POST','api/logout');
        $response->assertStatus(401);
    }
}
