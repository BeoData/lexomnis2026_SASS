<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SubscriptionCrudTest extends TestCase
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

    public function test_it_can_display_subscriptions_index_page()
    {
        // Mock API response
        Http::fake([
            '*/api/admin/subscriptions*' => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'firm_id' => 1,
                        'plan_id' => 1,
                        'status' => 'active',
                        'starts_at' => '2026-01-01',
                        'ends_at' => '2026-12-31',
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->get('/subscriptions');

        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.index');
        $response->assertViewHas('subscriptions');
    }

    public function test_it_can_display_subscription_details()
    {
        $subscriptionId = 1;
        
        // Mock API response
        Http::fake([
            "*/api/admin/subscriptions/{$subscriptionId}" => Http::response([
                'id' => $subscriptionId,
                'firm_id' => 1,
                'plan_id' => 1,
                'status' => 'active',
                'starts_at' => '2026-01-01',
                'ends_at' => '2026-12-31',
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->get("/subscriptions/{$subscriptionId}");

        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.show');
        $response->assertViewHas('subscription');
    }

    public function test_it_handles_api_errors_gracefully()
    {
        // Mock API error response
        Http::fake([
            '*/api/admin/subscriptions*' => Http::response([
                'message' => 'API Error',
            ], 500),
        ]);

        $response = $this->actingAs($this->user)
            ->get('/subscriptions');

        $response->assertSessionHasErrors(['error']);
    }

    public function test_it_requires_authentication()
    {
        $response = $this->get('/subscriptions');
        $response->assertRedirect('/login');

        $response = $this->get('/subscriptions/1');
        $response->assertRedirect('/login');
    }
}

