<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TenantCrudTest extends TestCase
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

    public function test_it_can_display_tenants_index_page()
    {
        // Mock API response
        Http::fake([
            '*/api/admin/tenants*' => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'name' => 'Test Tenant',
                        'email' => 'test@example.com',
                        'status' => 'active',
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->get('/tenants');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Tenants/Index'));
    }

    public function test_it_can_display_create_tenant_page()
    {
        $response = $this->actingAs($this->user)
            ->get('/tenants/create');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Tenants/Create'));
    }

    public function test_it_can_create_a_new_tenant()
    {
        // Mock successful API response
        Http::fake([
            '*/api/admin/tenants' => Http::response([
                'id' => 1,
                'name' => 'New Tenant',
                'email' => 'newtenant@example.com',
                'status' => 'active',
            ], 201),
        ]);

        $tenantData = [
            'name' => 'New Tenant',
            'email' => 'newtenant@example.com',
            'phone' => '+381123456789',
            'country' => 'RS',
            'timezone' => 'Europe/Belgrade',
            'currency' => 'RSD',
        ];

        $response = $this->actingAs($this->user)
            ->post('/tenants', $tenantData);

        $response->assertRedirect(route('tenants.index'));
        $response->assertSessionHas('success', 'Tenant created successfully');

        // Verify API was called
        Http::assertSent(function ($request) use ($tenantData) {
            return $request->url() === config('services.tenant_app.url') . '/api/admin/tenants'
                && $request->method() === 'POST'
                && $request['name'] === $tenantData['name']
                && $request['email'] === $tenantData['email'];
        });
    }

    public function test_it_validates_required_fields_when_creating_tenant()
    {
        $response = $this->actingAs($this->user)
            ->post('/tenants', []);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_it_validates_email_format_when_creating_tenant()
    {
        $response = $this->actingAs($this->user)
            ->post('/tenants', [
                'name' => 'Test Tenant',
                'email' => 'invalid-email',
            ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_it_can_display_tenant_details()
    {
        $tenantId = 1;
        
        // Mock API response
        Http::fake([
            "*/api/admin/tenants/{$tenantId}" => Http::response([
                'id' => $tenantId,
                'name' => 'Test Tenant',
                'email' => 'test@example.com',
                'status' => 'active',
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->get("/tenants/{$tenantId}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Tenants/Show')
                ->has('tenant')
        );
    }

    public function test_it_can_display_edit_tenant_page()
    {
        $tenantId = 1;
        
        // Mock API response
        Http::fake([
            "*/api/admin/tenants/{$tenantId}" => Http::response([
                'id' => $tenantId,
                'name' => 'Test Tenant',
                'email' => 'test@example.com',
                'status' => 'active',
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->get("/tenants/{$tenantId}/edit");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Tenants/Edit')
                ->has('tenant')
        );
    }

    public function test_it_can_update_tenant()
    {
        $tenantId = 1;
        
        // Mock API response
        Http::fake([
            "*/api/admin/tenants/{$tenantId}" => Http::response([
                'id' => $tenantId,
                'name' => 'Updated Tenant',
                'email' => 'updated@example.com',
                'status' => 'active',
            ], 200),
        ]);

        $updateData = [
            'name' => 'Updated Tenant',
            'email' => 'updated@example.com',
            'phone' => '+381987654321',
        ];

        $response = $this->actingAs($this->user)
            ->put("/tenants/{$tenantId}", $updateData);

        $response->assertRedirect(route('tenants.show', $tenantId));
        $response->assertSessionHas('success', 'Tenant updated successfully');

        // Verify API was called
        Http::assertSent(function ($request) use ($tenantId, $updateData) {
            return str_contains($request->url(), "/api/admin/tenants/{$tenantId}")
                && $request->method() === 'PUT'
                && $request['name'] === $updateData['name'];
        });
    }

    public function test_it_can_suspend_tenant()
    {
        $tenantId = 1;
        
        // Mock API response
        Http::fake([
            "*/api/admin/tenants/{$tenantId}/suspend" => Http::response([
                'message' => 'Tenant suspended successfully',
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->post("/tenants/{$tenantId}/suspend");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Tenant suspended successfully');
    }

    public function test_it_can_activate_tenant()
    {
        $tenantId = 1;
        
        // Mock API response
        Http::fake([
            "*/api/admin/tenants/{$tenantId}/activate" => Http::response([
                'message' => 'Tenant activated successfully',
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->post("/tenants/{$tenantId}/activate");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Tenant activated successfully');
    }

    public function test_it_handles_api_errors_gracefully()
    {
        // Mock API error response
        Http::fake([
            '*/api/admin/tenants' => Http::response([
                'message' => 'API Error',
            ], 500),
        ]);

        $response = $this->actingAs($this->user)
            ->post('/tenants', [
                'name' => 'Test Tenant',
            ]);

        $response->assertSessionHasErrors(['error']);
    }

    public function test_it_requires_authentication()
    {
        $response = $this->get('/tenants');
        $response->assertRedirect('/login');

        $response = $this->get('/tenants/create');
        $response->assertRedirect('/login');

        $response = $this->post('/tenants', []);
        $response->assertRedirect('/login');
    }
}
