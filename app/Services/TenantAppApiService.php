<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TenantAppApiService
{
    protected string $baseUrl;
    protected string $apiToken;
    protected int $timeout;
    protected int $retryAttempts;

    public function __construct()
    {
        $this->baseUrl = config('services.tenant_app.url', env('TENANT_APP_URL', 'http://localhost:8000'));
        $this->apiToken = config('services.tenant_app.api_token', env('TENANT_APP_API_TOKEN'));
        $this->timeout = config('services.tenant_app.timeout', 30);
        $this->retryAttempts = config('services.tenant_app.retry_attempts', 3);
    }

    /**
     * Make API request with retry logic
     */
    protected function request(string $method, string $endpoint, array $data = [], array $headers = []): array
    {
        $url = rtrim($this->baseUrl, '/') . '/api/admin/' . ltrim($endpoint, '/');
        
        $defaultHeaders = [
            'Authorization' => 'Bearer ' . $this->apiToken,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $headers = array_merge($defaultHeaders, $headers);

        $attempt = 0;
        $lastException = null;

        while ($attempt < $this->retryAttempts) {
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

                // If 401 or 403, don't retry
                if (in_array($response->status(), [401, 403])) {
                    return [
                        'success' => false,
                        'error' => $response->json()['message'] ?? 'Unauthorized',
                        'status' => $response->status(),
                    ];
                }

                // Retry on server errors
                if ($response->serverError() && $attempt < $this->retryAttempts - 1) {
                    $attempt++;
                    sleep(pow(2, $attempt)); // Exponential backoff
                    continue;
                }

                return [
                    'success' => false,
                    'error' => $response->json()['message'] ?? 'Request failed',
                    'status' => $response->status(),
                    'data' => $response->json(),
                ];

            } catch (\Exception $e) {
                $lastException = $e;
                $attempt++;
                
                if ($attempt < $this->retryAttempts) {
                    sleep(pow(2, $attempt));
                }
            }
        }

        Log::error('TenantAppApiService request failed', [
            'method' => $method,
            'endpoint' => $endpoint,
            'attempts' => $attempt,
            'error' => $lastException?->getMessage(),
        ]);

        return [
            'success' => false,
            'error' => $lastException?->getMessage() ?? 'Request failed after retries',
            'status' => 500,
        ];
    }

    // Tenant Management
    public function getTenants(array $filters = []): array
    {
        return $this->request('GET', 'tenants', $filters);
    }

    public function getTenant(int $id): array
    {
        return $this->request('GET', "tenants/{$id}");
    }

    public function createTenant(array $data): array
    {
        return $this->request('POST', 'tenants', $data);
    }

    public function updateTenant(int $id, array $data): array
    {
        return $this->request('PUT', "tenants/{$id}", $data);
    }

    public function suspendTenant(int $id): array
    {
        return $this->request('POST', "tenants/{$id}/suspend");
    }

    public function activateTenant(int $id): array
    {
        return $this->request('POST', "tenants/{$id}/activate");
    }

    public function deleteTenant(int $id): array
    {
        return $this->request('DELETE', "tenants/{$id}");
    }

    // User Management
    public function getUsers(array $filters = []): array
    {
        return $this->request('GET', 'users', $filters);
    }

    public function getUser(int $id): array
    {
        return $this->request('GET', "users/{$id}");
    }

    public function updateUser(int $id, array $data): array
    {
        return $this->request('PUT', "users/{$id}", $data);
    }

    public function suspendUser(int $id): array
    {
        return $this->request('POST', "users/{$id}/suspend");
    }

    public function forceLogoutUser(int $id): array
    {
        return $this->request('POST', "users/{$id}/force-logout");
    }

    public function resetUserPassword(int $id): array
    {
        return $this->request('POST', "users/{$id}/reset-password");
    }

    // Impersonation
    public function generateImpersonationToken(int $userId, bool $readOnly = false): array
    {
        return $this->request('POST', "users/{$userId}/impersonate", ['read_only' => $readOnly]);
    }

    // Subscriptions
    public function getSubscriptions(array $filters = []): array
    {
        return $this->request('GET', 'subscriptions', $filters);
    }

    public function getSubscription(int $id): array
    {
        return $this->request('GET', "subscriptions/{$id}");
    }

    public function createSubscription(array $data): array
    {
        return $this->request('POST', 'subscriptions', $data);
    }

    public function updateSubscription(int $id, array $data): array
    {
        return $this->request('PUT', "subscriptions/{$id}", $data);
    }

    // Plans
    public function getPlans(array $filters = []): array
    {
        return $this->request('GET', 'plans', $filters);
    }

    public function getPlan(int $id): array
    {
        return $this->request('GET', "plans/{$id}");
    }

    public function createPlan(array $data): array
    {
        return $this->request('POST', 'plans', $data);
    }

    public function updatePlan(int $id, array $data): array
    {
        return $this->request('PUT', "plans/{$id}", $data);
    }

    // Feature Flags
    public function getFeatureFlags(array $filters = []): array
    {
        return $this->request('GET', 'feature-flags', $filters);
    }

    public function getTenantFeatureFlags(int $tenantId): array
    {
        return $this->request('GET', "feature-flags/tenant/{$tenantId}");
    }

    public function createFeatureFlag(array $data): array
    {
        return $this->request('POST', 'feature-flags', $data);
    }

    public function updateFeatureFlag(int $id, array $data): array
    {
        return $this->request('PUT', "feature-flags/{$id}", $data);
    }

    // System Monitoring
    public function getSystemHealth(): array
    {
        return $this->request('GET', 'system/health');
    }

    public function getQueueStatus(): array
    {
        return $this->request('GET', 'system/queues');
    }

    public function getCronStatus(): array
    {
        return $this->request('GET', 'system/crons');
    }

    public function getMetrics(): array
    {
        return $this->request('GET', 'system/metrics');
    }

    public function getActivityLogs(array $filters = []): array
    {
        return $this->request('GET', 'system/activity-logs', $filters);
    }

    // Audit Logs
    public function getAuditLogs(array $filters = []): array
    {
        return $this->request('GET', 'audit-logs', $filters);
    }

    public function getAuditLog(int $id): array
    {
        return $this->request('GET', "audit-logs/{$id}");
    }
}

