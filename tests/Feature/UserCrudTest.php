<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create super admin user for testing
        $this->user = User::factory()->create([
            'email' => 'superadmin@lexomnis.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_it_can_display_users_index_page()
    {
        // Mock API response
        Http::fake([
            '*/api/admin/users*' => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'name' => 'Test User',
                        'email' => 'test@example.com',
                        'firm_id' => 1,
                        'role' => 'admin',
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->get('/users');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Users/Index'));
    }

    public function test_it_can_display_user_details()
    {
        $userId = 1;
        
        // Mock API response
        Http::fake([
            "*/api/admin/users/{$userId}" => Http::response([
                'id' => $userId,
                'name' => 'Test User',
                'email' => 'test@example.com',
                'firm_id' => 1,
                'role' => 'admin',
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->get("/users/{$userId}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Users/Show')
                ->has('user')
        );
    }

    public function test_it_can_suspend_user()
    {
        $userId = 1;
        
        // Mock API response
        Http::fake([
            "*/api/admin/users/{$userId}/suspend" => Http::response([
                'message' => 'User suspended successfully',
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->post("/users/{$userId}/suspend");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'User suspended successfully');
    }

    public function test_it_can_reset_user_password()
    {
        $userId = 1;
        
        // Mock API response
        Http::fake([
            "*/api/admin/users/{$userId}/reset-password" => Http::response([
                'message' => 'Password reset successfully',
                'temporary_password' => 'temp123',
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->post("/users/{$userId}/reset-password");

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_it_can_generate_impersonation_token()
    {
        $userId = 1;
        
        // Mock API response
        Http::fake([
            "*/api/admin/users/{$userId}/impersonate" => Http::response([
                'token' => 'impersonation-token-123',
                'impersonate_url' => 'http://localhost:8000/impersonate/impersonation-token-123',
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->post("/users/{$userId}/impersonate");

        $response->assertRedirect('http://localhost:8000/impersonate/impersonation-token-123');
    }

    public function test_it_handles_api_errors_gracefully()
    {
        // Mock API error response
        Http::fake([
            '*/api/admin/users*' => Http::response([
                'message' => 'API Error',
            ], 500),
        ]);

        $response = $this->actingAs($this->user)
            ->get('/users');

        $response->assertSessionHasErrors(['error']);
    }

    public function test_it_requires_authentication()
    {
        $response = $this->get('/users');
        $response->assertRedirect('/login');

        $response = $this->get('/users/1');
        $response->assertRedirect('/login');

        $response = $this->post('/users/1/suspend');
        $response->assertRedirect('/login');
    }
}

