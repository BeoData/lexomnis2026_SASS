<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TenantAppApiService;
use Mockery;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    public function test_superadmin_role_can_access_dashboard_regardless_of_email(): void
    {
        $api = Mockery::mock(TenantAppApiService::class);
        $api->shouldReceive('getDashboardData')->once()->andReturn([]);
        $this->app->instance(TenantAppApiService::class, $api);

        $user = User::factory()->superAdmin()->create(['email' => 'owner@example.test']);

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_email_or_domain_cannot_replace_superadmin_role(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@lexomnis.rs',
            'role' => 'client',
        ]);

        $this->actingAs($user)->get('/dashboard')
            ->assertRedirect(route('client.dashboard'));
    }
}
