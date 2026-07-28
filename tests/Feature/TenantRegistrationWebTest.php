<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

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
            '*/api/public/options/countries' => Http::response($this->countriesResponse()),
            '*/api/public/tenants/register' => Http::response([
                'success' => true,
                'data' => [
                    'payment_url' => $mockPaymentUrl,
                    'id' => 1,
                    'name' => 'Test Firm',
                    'slug' => 'test-firm',
                    'status' => 'pending',
                ],
            ], 200),
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
            'vat_status' => 'registered',
            'registration_type' => 'paid',
            'plan_id' => 1,
            'billing_period' => 'monthly',
            'payment_method' => 'stripe',
        ];

        $response = $this->post('/register', $registrationData);

        // Should redirect to the payment URL
        $response->assertRedirect($mockPaymentUrl);

        // Should create user in SASS database
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'role' => 'client',
        ]);
    }

    /**
     * Test that trial registration redirects to success page
     */
    public function test_trial_registration_redirects_to_success_page()
    {
        // Mock the tenant app API response
        Http::fake([
            '*/api/public/options/countries' => Http::response($this->countriesResponse()),
            '*/api/public/tenants/register' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 1,
                    'name' => 'Test Firm',
                    'slug' => 'test-firm',
                    'status' => 'active',
                ],
            ], 200),
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
            'vat_status' => 'not_registered',
            'registration_type' => 'trial',
            'trial_days' => 30,
        ];

        $response = $this->post('/register', $registrationData);

        // Should redirect to success page
        $response->assertRedirect(route('tenant.register.success'));

        // Should create user in SASS database
        $this->assertDatabaseHas('users', [
            'email' => 'trial@example.com',
            'name' => 'Test User',
            'role' => 'client',
        ]);
    }

    /**
     * Test that paid registration fails when API doesn't return payment URL
     */
    public function test_paid_registration_fails_when_no_payment_url()
    {
        // Mock the tenant app API response without payment_url
        Http::fake([
            '*/api/public/options/countries' => Http::response($this->countriesResponse()),
            '*/api/public/tenants/register' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 1,
                    'name' => 'Test Firm',
                    'slug' => 'test-firm',
                    'status' => 'pending',
                    // No payment_url field
                ],
            ], 200),
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
            'vat_status' => 'registered',
            'registration_type' => 'paid',
            'plan_id' => 1,
            'billing_period' => 'monthly',
            'payment_method' => 'stripe',
        ];

        $response = $this->post('/register', $registrationData);

        // Should redirect back with error
        $response->assertRedirect();
        $response->assertSessionHasErrors('error');

        // Should still create user in SASS database
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'role' => 'client',
        ]);
    }

    /**
     * Test that registration fails when API returns error
     */
    public function test_registration_fails_when_api_returns_error()
    {
        // Mock the tenant app API response with error
        Http::fake([
            '*/api/public/options/countries' => Http::response($this->countriesResponse()),
            '*/api/public/tenants/register' => Http::response([
                'success' => false,
                'errors' => [
                    'email' => ['Email already exists'],
                ],
            ], 422),
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
            'vat_status' => 'not_registered',
            'registration_type' => 'paid',
            'plan_id' => 1,
            'billing_period' => 'monthly',
            'payment_method' => 'stripe',
        ];

        $response = $this->post('/register', $registrationData);

        // Should redirect back with error
        $response->assertRedirect();
        $response->assertSessionHasErrors('email');

        // Should not create user in SASS database
        $this->assertDatabaseMissing('users', [
            'email' => 'existing@example.com',
        ]);
    }

    public function test_registration_requires_vat_status_and_forwards_it_to_main(): void
    {
        Http::fake([
            '*/api/public/options/countries' => Http::response($this->countriesResponse()),
            '*/api/public/tenants/register' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 10,
                    'name' => 'VAT Firm',
                    'slug' => 'vat-firm',
                    'status' => 'active',
                ],
            ], 201),
        ]);

        $payload = [
            'first_name' => 'VAT',
            'last_name' => 'Owner',
            'email' => 'vat-owner@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'country' => 'RS',
            'timezone' => 'Europe/Belgrade',
            'currency' => 'RSD',
            'registration_type' => 'trial',
        ];

        $this->post('/register', $payload)
            ->assertSessionHasErrors('vat_status');

        $this->post('/register', [...$payload, 'vat_status' => 'registered'])
            ->assertRedirect(route('tenant.register.success'))
            ->assertSessionDoesntHaveErrors();

        Http::assertSent(fn ($request): bool => str_ends_with($request->url(), '/api/public/tenants/register')
            && $request['vat_status'] === 'registered'
            && $request['country'] === 'RS');
    }

    public function test_registration_page_loads_country_options_and_localized_texts(): void
    {
        Http::fake([
            '*/api/admin/plans*' => Http::response([
                'success' => true,
                'data' => [],
            ]),
            '*/api/public/options/countries' => Http::response($this->countriesResponse()),
        ]);

        $this->get('/register')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('TenantRegistration/Register')
                ->has('countries', 2)
                ->where('countries.0.value', 'RS')
                ->where('registration_texts.country_label', 'Firm country')
                ->where('registration_texts.country_placeholder', 'Select country')
            );

        Http::assertSent(fn ($request): bool => str_ends_with(
            $request->url(),
            '/api/public/options/countries'
        ));
    }

    public function test_country_is_required_validated_against_main_and_has_no_rs_fallback(): void
    {
        Http::fake([
            '*/api/public/options/countries' => Http::response($this->countriesResponse()),
            '*/api/public/tenants/register' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 11,
                    'name' => 'Country Firm',
                    'slug' => 'country-firm',
                    'status' => 'active',
                ],
            ], 201),
        ]);

        $payload = [
            'first_name' => 'Country',
            'last_name' => 'Owner',
            'email' => 'country-owner@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'timezone' => 'Europe/Belgrade',
            'currency' => 'RSD',
            'vat_status' => 'registered',
            'registration_type' => 'trial',
        ];

        $this->post('/register', $payload)
            ->assertSessionHasErrors('country');

        $this->post('/register', [...$payload, 'country' => 'XX'])
            ->assertSessionHasErrors('country');

        $this->post('/register', [...$payload, 'country' => 'BA'])
            ->assertRedirect(route('tenant.register.success'))
            ->assertSessionDoesntHaveErrors();

        Http::assertSent(fn ($request): bool => str_ends_with($request->url(), '/api/public/tenants/register')
            && $request['country'] === 'BA'
            && $request['vat_status'] === 'registered');
    }

    private function countriesResponse(): array
    {
        return [
            'success' => true,
            'data' => [
                [
                    'id' => 1,
                    'value' => 'RS',
                    'label' => 'Serbia',
                    'metadata' => null,
                ],
                [
                    'id' => 2,
                    'value' => 'BA',
                    'label' => 'Bosnia and Herzegovina',
                    'metadata' => null,
                ],
            ],
        ];
    }
}
