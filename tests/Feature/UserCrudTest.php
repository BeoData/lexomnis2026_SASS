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
        $tenantId = 1;

        // Mock API response
        Http::fake([
            '*/api/admin/users*' => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'name' => 'Test User',
                        'email' => 'test@example.com',
                        'firm_id' => $tenantId,
                        'role' => 'admin',
                    ],
                ],
            ], 200),
            '*/api/admin/tenants*' => Http::response([
                'data' => [
                    [
                        'id' => $tenantId,
                        'name' => 'Test Tenant',
                        'status' => 'active',
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->get("/users?tenant_id={$tenantId}");

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
        $response->assertViewHas('users');
        $response->assertViewHas('tenants');
    }

    public function test_it_can_display_user_details()
    {
        $userId = 1;
        $tenantId = 1;
        
        // Mock API response
        Http::fake([
            "*/api/admin/users/{$userId}" => Http::response([
                'id' => $userId,
                'name' => 'Test User',
                'email' => 'test@example.com',
                'firm_id' => $tenantId,
                'role' => 'admin',
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->get("/users/{$userId}?tenant_id={$tenantId}");

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.show');
        $response->assertViewHas('user');
    }

    public function test_it_can_suspend_user()
    {
        $userId = 1;
        $tenantId = 1;
        
        // Mock API response
        Http::fake([
            "*/api/admin/users/{$userId}/suspend" => Http::response([
                'message' => 'User suspended successfully',
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->post("/users/{$userId}/suspend", ['tenant_id' => $tenantId]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'User suspended successfully');
    }

    public function test_it_can_reset_user_password()
    {
        $userId = 1;
        $tenantId = 1;
        
        // Mock API response
        Http::fake([
            "*/api/admin/users/{$userId}/reset-password" => Http::response([
                'message' => 'Password reset successfully',
                'temporary_password' => 'temp123',
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->post("/users/{$userId}/reset-password", ['tenant_id' => $tenantId]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_it_can_generate_impersonation_token()
    {
        $userId = 1;
        $tenantId = 1;
        
        // Mock API response
        Http::fake([
            "*/api/admin/users/{$userId}/impersonate" => Http::response([
                'token' => 'impersonation-token-123',
                'impersonate_url' => 'http://localhost:8000/impersonate/impersonation-token-123',
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->post("/users/{$userId}/impersonate", ['tenant_id' => $tenantId]);

        $response->assertRedirect('http://localhost:8000/impersonate/impersonation-token-123');
    }

    public function test_it_handles_api_errors_gracefully()
    {
        $tenantId = 1;

        // Mock API error response
        Http::fake([
            '*/api/admin/users*' => Http::response([
                'message' => 'API Error',
            ], 500),
            '*/api/admin/tenants*' => Http::response([
                'data' => [
                    [
                        'id' => $tenantId,
                        'name' => 'Test Tenant',
                        'status' => 'active',
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->get("/users?tenant_id={$tenantId}");

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

