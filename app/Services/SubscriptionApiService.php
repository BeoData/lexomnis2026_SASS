<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubscriptionApiService
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.tenant_app.url', env('TENANT_APP_URL', 'http://localhost:8000'));
        $this->timeout = config('services.tenant_app.timeout', 30);
    }

    /**
     * Make API request to public subscription endpoints
     */
    protected function request(string $method, string $endpoint, array $data = [], array $headers = []): array
    {
        $url = rtrim($this->baseUrl, '/') . '/api/subscriptions/' . ltrim($endpoint, '/');
        
        $defaultHeaders = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        // Note: For cross-domain requests, we need to handle authentication differently
        // If SaaS App and Core App are on the same domain, session cookies will work
        // Otherwise, we may need to implement token-based auth or use Inertia for these calls
        $headers = array_merge($defaultHeaders, $headers);

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders($headers)
                ->{strtolower($method)}($url, $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'status' => $response->status(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Request failed',
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('SubscriptionApiService request failed', [
                'method' => $method,
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getPlans(): array
    {
        return $this->request('GET', 'plans');
    }

    public function createCheckout(array $data): array
    {
        return $this->request('POST', 'checkout', $data);
    }

    public function getCurrentSubscription(): array
    {
        return $this->request('GET', 'current');
    }

    public function upgrade(array $data): array
    {
        return $this->request('POST', 'upgrade', $data);
    }

    public function downgrade(array $data): array
    {
        return $this->request('POST', 'downgrade', $data);
    }

    public function cancel(): array
    {
        return $this->request('POST', 'cancel');
    }

    public function getInvoices(): array
    {
        return $this->request('GET', 'invoices');
    }
}
