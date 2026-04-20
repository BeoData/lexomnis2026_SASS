<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class TenantRegistrationWebTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that paid registration redirects to payment URL when API returns success
     */
    public function test_paid_registration_redirects_to_payment_url()
    {
        // Mock the tenant app API response
        $mockPaymentUrl = 'https://checkout.stripe.com/test_session_123';

        Http::fake([
            '*/api/public/tenants/register' => Http::response([
                'success' => true,
                'data' => [
                    'payment_url' => $mockPaymentUrl,
                    'id' => 1,
                    'name' => 'Test Firm',
                    'slug' => 'test-firm',
                    'status' => 'pending'
                ]
            ], 200)
        ]);

        $registrationData = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+38162271888',
            'country' => 'RS',
            'timezone' => 'Europe/Belgrade',
            'currency' => 'RSD',
            'registration_type' => 'paid',
            'plan_id' => 1,
            'billing_period' => 'monthly',
            'payment_method' => 'stripe'
        ];

        $response = $this->post('/register', $registrationData);

        // Should redirect to the payment URL
        $response->assertRedirect($mockPaymentUrl);

        // Should create user in SASS database
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'role' => 'client'
        ]);
    }

    /**
     * Test that trial registration redirects to success page
     */
    public function test_trial_registration_redirects_to_success_page()
    {
        // Mock the tenant app API response
        Http::fake([
            '*/api/public/tenants/register' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 1,
                    'name' => 'Test Firm',
                    'slug' => 'test-firm',
                    'status' => 'active'
                ]
            ], 200)
        ]);

        $registrationData = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'trial@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+38162271888',
            'country' => 'RS',
            'timezone' => 'Europe/Belgrade',
            'currency' => 'RSD',
            'registration_type' => 'trial',
            'trial_days' => 30
        ];

        $response = $this->post('/register', $registrationData);

        // Should redirect to success page
        $response->assertRedirect(route('tenant.register.success'));

        // Should create user in SASS database
        $this->assertDatabaseHas('users', [
            'email' => 'trial@example.com',
            'name' => 'Test User',
            'role' => 'client'
        ]);
    }

    /**
     * Test that paid registration fails when API doesn't return payment URL
     */
    public function test_paid_registration_fails_when_no_payment_url()
    {
        // Mock the tenant app API response without payment_url
        Http::fake([
            '*/api/public/tenants/register' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 1,
                    'name' => 'Test Firm',
                    'slug' => 'test-firm',
                    'status' => 'pending'
                    // No payment_url field
                ]
            ], 200)
        ]);

        $registrationData = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+38162271888',
            'country' => 'RS',
            'timezone' => 'Europe/Belgrade',
            'currency' => 'RSD',
            'registration_type' => 'paid',
            'plan_id' => 1,
            'billing_period' => 'monthly',
            'payment_method' => 'stripe'
        ];

        $response = $this->post('/register', $registrationData);

        // Should redirect back with error
        $response->assertRedirect();
        $response->assertSessionHasErrors('error');

        // Should still create user in SASS database
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'role' => 'client'
        ]);
    }

    /**
     * Test that registration fails when API returns error
     */
    public function test_registration_fails_when_api_returns_error()
    {
        // Mock the tenant app API response with error
        Http::fake([
            '*/api/public/tenants/register' => Http::response([
                'success' => false,
                'errors' => [
                    'email' => ['Email already exists']
                ]
            ], 422)
        ]);

        $registrationData = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+38162271888',
            'country' => 'RS',
            'timezone' => 'Europe/Belgrade',
            'currency' => 'RSD',
            'registration_type' => 'paid',
            'plan_id' => 1,
            'billing_period' => 'monthly',
            'payment_method' => 'stripe'
        ];

        $response = $this->post('/register', $registrationData);

        // Should redirect back with error
        $response->assertRedirect();
        $response->assertSessionHasErrors('email');

        // Should not create user in SASS database
        $this->assertDatabaseMissing('users', [
            'email' => 'existing@example.com'
        ]);
    }
}