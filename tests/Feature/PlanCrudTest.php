<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PlanCrudTest extends TestCase
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

    public function test_it_can_display_plans_index_page()
    {
        // Mock API response
        Http::fake([
            '*/api/admin/plans*' => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'name' => 'Basic Plan',
                        'price' => 29.99,
                        'billing_period' => 'monthly',
                        'is_active' => true,
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->get('/plans');

        $response->assertStatus(200);
        $response->assertViewIs('admin.plans.index');
        $response->assertViewHas('plans');
    }

    public function test_it_can_display_create_plan_page()
    {
        $response = $this->actingAs($this->user)
            ->get('/plans/create');

        $response->assertStatus(200);
        $response->assertViewIs('admin.plans.create');
    }

    public function test_it_can_create_a_new_plan()
    {
        // Mock successful API response
        Http::fake([
            '*/api/admin/plans' => Http::response([
                'id' => 1,
                'name' => 'New Plan',
                'price' => 49.99,
                'billing_period' => 'monthly',
                'is_active' => true,
            ], 201),
        ]);

        $planData = [
            'name' => 'New Plan',
            'description' => 'A new plan',
            'price' => 49.99,
            'billing_cycle' => 'monthly',
            'currency' => 'USD',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post('/plans', $planData);

        $response->assertRedirect(route('plans.index'));
        $response->assertSessionHas('success', 'Plan created successfully');
    }

    public function test_it_validates_required_fields_when_creating_plan()
    {
        $response = $this->actingAs($this->user)
            ->post('/plans', []);

        $response->assertSessionHasErrors(['name', 'price', 'billing_cycle']);
    }

    public function test_it_validates_price_is_numeric_when_creating_plan()
    {
        $response = $this->actingAs($this->user)
            ->post('/plans', [
                'name' => 'Test Plan',
                'price' => 'invalid',
                'billing_cycle' => 'monthly',
            ]);

        $response->assertSessionHasErrors(['price']);
    }

    public function test_it_can_display_plan_details()
    {
        $planId = 1;
        
        // Mock API response
        Http::fake([
            "*/api/admin/plans/{$planId}" => Http::response([
                'id' => $planId,
                'name' => 'Test Plan',
                'price' => 29.99,
                'billing_period' => 'monthly',
                'is_active' => true,
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->get("/plans/{$planId}");

        $response->assertStatus(200);
        $response->assertViewIs('admin.plans.show');
        $response->assertViewHas('plan');
    }

    public function test_it_can_display_edit_plan_page()
    {
        $planId = 1;
        
        // Mock API response
        Http::fake([
            "*/api/admin/plans/{$planId}" => Http::response([
                'id' => $planId,
                'name' => 'Test Plan',
                'price' => 29.99,
                'billing_period' => 'monthly',
                'is_active' => true,
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->get("/plans/{$planId}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('admin.plans.edit');
        $response->assertViewHas('plan');
    }

    public function test_it_can_update_plan()
    {
        $planId = 1;
        
        // Mock API response
        Http::fake([
            "*/api/admin/plans/{$planId}" => Http::response([
                'id' => $planId,
                'name' => 'Updated Plan',
                'price' => 59.99,
                'billing_period' => 'yearly',
                'is_active' => true,
            ], 200),
        ]);

        $updateData = [
            'name' => 'Updated Plan',
            'price' => 59.99,
            'billing_cycle' => 'yearly',
        ];

        $response = $this->actingAs($this->user)
            ->put("/plans/{$planId}", $updateData);

        $response->assertRedirect(route('plans.show', $planId));
        $response->assertSessionHas('success', 'Plan updated successfully');
    }

    public function test_it_handles_api_errors_gracefully()
    {
        // Mock API error response
        Http::fake([
            '*/api/admin/plans' => Http::response([
                'message' => 'API Error',
            ], 500),
        ]);

        $response = $this->actingAs($this->user)
            ->post('/plans', [
                'name' => 'Test Plan',
                'price' => 29.99,
                'billing_cycle' => 'monthly',
            ]);

        $response->assertSessionHasErrors(['error']);
    }

    public function test_it_requires_authentication()
    {
        $response = $this->get('/plans');
        $response->assertRedirect('/login');

        $response = $this->get('/plans/create');
        $response->assertRedirect('/login');

        $response = $this->post('/plans', []);
        $response->assertRedirect('/login');
    }
}

