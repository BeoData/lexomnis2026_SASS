<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TenantAppApiService
{
    protected string $baseUrl;
    protected ?string $apiToken;
    protected int $timeout;
    protected int $retryAttempts;
    protected int $connectTimeout;
    protected int $dashboardTimeout;

    public function __construct()
    {
        $this->baseUrl = config('services.tenant_app.url', 'http://localhost:8000');
        $this->apiToken = config('services.tenant_app.api_token');

        $timeoutRaw = (int) config('services.tenant_app.timeout', 30);
        $this->timeout = min((int) config('services.tenant_app.timeout_max_cap', 15), $timeoutRaw);

        $this->retryAttempts = (int) config('services.tenant_app.retry_attempts', 2);

        $this->connectTimeout = (int) config('services.tenant_app.connect_timeout', 5);
        $this->dashboardTimeout = min(
            (int) config('services.tenant_app.dashboard_timeout', 8),
            $this->timeout
        );
    }

    /**
     * Make API request with retry logic
     */
    protected function request(string $method, string $endpoint, array $data = [], array $headers = []): array
    {
        if (!$this->apiToken) {
            return [
                'success' => false,
                'error' => 'API token is not configured. Set it in Admin → Settings.',
                'status' => 500,
            ];
        }

        $pathPrefix = rtrim(config('services.tenant_app.api_path_prefix', 'api/admin'), '/');
        $url = rtrim($this->baseUrl, '/') . '/' . $pathPrefix . '/' . ltrim($endpoint, '/');

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
                Log::info('TenantAppApiService making request', [
                    'method' => $method,
                    'url' => $url,
                    'data' => $data,
                    'attempt' => $attempt + 1,
                ]);

                $response = Http::connectTimeout($this->connectTimeout)
                            ->timeout($this->timeout)
                            ->withHeaders($headers)
                    ->{strtolower($method)}($url, $data);

                Log::info('TenantAppApiService response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'successful' => $response->successful(),
                ]);

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

    public function assignPlanToTenant(int $id, int $planId, ?string $billingPeriod = null): array
    {
        $data = ['plan_id' => $planId];
        if ($billingPeriod) {
            $data['billing_period'] = $billingPeriod;
        }
        return $this->request('POST', "tenants/{$id}/assign-plan", $data);
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

    public function getUser(int $id, ?int $tenantId = null): array
    {
        $payload = $tenantId ? ['tenant_id' => $tenantId] : [];
        return $this->request('GET', "users/{$id}", $payload);
    }

    public function updateUser(int $id, array $data, ?int $tenantId = null): array
    {
        if ($tenantId) {
            $data['tenant_id'] = $tenantId;
        }
        return $this->request('PUT', "users/{$id}", $data);
    }

    public function suspendUser(int $id, ?int $tenantId = null): array
    {
        $payload = $tenantId ? ['tenant_id' => $tenantId] : [];
        return $this->request('POST', "users/{$id}/suspend", $payload);
    }

    public function forceLogoutUser(int $id, ?int $tenantId = null): array
    {
        $payload = $tenantId ? ['tenant_id' => $tenantId] : [];
        return $this->request('POST', "users/{$id}/force-logout", $payload);
    }

    public function resetUserPassword(int $id, ?int $tenantId = null): array
    {
        $payload = $tenantId ? ['tenant_id' => $tenantId] : [];
        return $this->request('POST', "users/{$id}/reset-password", $payload);
    }

    // Impersonation
    public function generateImpersonationToken(int $userId, bool $readOnly = false, ?int $tenantId = null): array
    {
        $payload = ['read_only' => $readOnly];
        if ($tenantId) {
            $payload['tenant_id'] = $tenantId;
        }
        return $this->request('POST', "users/{$userId}/impersonate", $payload);
    }

    public function generateTenantImpersonationToken(int $tenantId, bool $readOnly = false): array
    {
        $payload = ['read_only' => $readOnly];
        return $this->request('POST', "tenants/{$tenantId}/impersonate", $payload);
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

    // Manual Payment Management
    public function getPendingManualPayments(array $filters = []): array
    {
        return $this->request('GET', 'payments/manual/pending', $filters);
    }

    public function approvePayment(int $id): array
    {
        return $this->request('POST', "payments/{$id}/approve");
    }

    public function rejectPayment(int $id, string $reason): array
    {
        return $this->request('POST', "payments/{$id}/reject", ['reason' => $reason]);
    }

    public function getPayment(int $id): array
    {
        return $this->request('GET', "payments/{$id}");
    }

    // Feature Flags
    public function getFeatureFlags(array $filters = []): array
    {
        return $this->request('GET', 'feature-flags', $filters);
    }

    public function getFeatureFlag(int $id): array
    {
        return $this->request('GET', "feature-flags/{$id}");
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

    /**
     * Fetch all dashboard data in one parallel batch (max wait = one request timeout).
     */
    public function getDashboardData(): array
    {
        if (!$this->apiToken) {
            return $this->emptyDashboardData();
        }

        $base = rtrim($this->baseUrl, '/') . '/' . ltrim(config('services.tenant_app.api_path_prefix', 'api/admin'), '/') . '/';
        $headers = [
            'Authorization' => 'Bearer ' . $this->apiToken,
            'Accept' => 'application/json',
        ];

        try {
            $responses = Http::connectTimeout($this->connectTimeout)
                ->timeout($this->dashboardTimeout)
                ->pool(fn(\Illuminate\Http\Client\Pool $pool) => [
                    $pool->as('tenants')->withHeaders($headers)->get($base . 'tenants', ['per_page' => 1]),
                    $pool->as('tenants_active')->withHeaders($headers)->get($base . 'tenants', ['status' => 'active', 'per_page' => 1]),
                    $pool->as('tenants_suspended')->withHeaders($headers)->get($base . 'tenants', ['status' => 'suspended', 'per_page' => 1]),
                    $pool->as('tenants_trial')->withHeaders($headers)->get($base . 'tenants', ['status' => 'trial', 'per_page' => 1]),
                    $pool->as('users')->withHeaders($headers)->get($base . 'users', ['per_page' => 1]),
                    $pool->as('subscriptions')->withHeaders($headers)->get($base . 'subscriptions', ['per_page' => 1]),
                    $pool->as('subscriptions_active')->withHeaders($headers)->get($base . 'subscriptions', ['status' => 'active', 'per_page' => 1]),
                    $pool->as('plans')->withHeaders($headers)->get($base . 'plans', ['per_page' => 1]),
                    $pool->as('health')->withHeaders($headers)->get($base . 'system/health'),
                    $pool->as('audit_logs')->withHeaders($headers)->get($base . 'audit-logs', ['per_page' => 10]),
                ]);

            $r = fn(string $key) => $responses[$key] ?? null;
            $isResponse = fn($v) => $v instanceof \Illuminate\Http\Client\Response;
            $json = fn($res) => $isResponse($res) && $res->successful() ? $res->json() : null;
            $ok = fn($res) => $isResponse($res) && $res->successful();

            $tenantsData = $json($r('tenants'));
            $total = $tenantsData['total'] ?? 0;

            return [
                'tenants' => [
                    'total' => $total,
                    'active' => $json($r('tenants_active'))['total'] ?? 0,
                    'suspended' => $json($r('tenants_suspended'))['total'] ?? 0,
                    'trial' => $json($r('tenants_trial'))['total'] ?? 0,
                ],
                'users' => [
                    'total' => $json($r('users'))['total'] ?? 0,
                    'active' => $json($r('users'))['total'] ?? 0,
                    'suspended' => 0,
                ],
                'subscriptions' => [
                    'total' => $json($r('subscriptions'))['total'] ?? 0,
                    'active' => $json($r('subscriptions_active'))['total'] ?? 0,
                    'expired' => 0,
                ],
                'plans' => [
                    'total' => $json($r('plans'))['total'] ?? 0,
                    'active' => $json($r('plans'))['total'] ?? 0,
                ],
                'system' => [
                    'status' => $ok($r('health')) ? 'healthy' : 'unhealthy',
                    'api_connected' => $ok($r('health')),
                ],
                'recentActivity' => $json($r('audit_logs'))['data']['data'] ?? [],
            ];
        } catch (\Throwable $e) {
            Log::warning('TenantAppApiService getDashboardData failed', ['message' => $e->getMessage()]);
            return $this->emptyDashboardData();
        }
    }

    /**
     * Default structure when API is unavailable or token missing.
     */
    protected function emptyDashboardData(): array
    {
        return [
            'tenants' => ['total' => 0, 'active' => 0, 'suspended' => 0, 'trial' => 0],
            'users' => ['total' => 0, 'active' => 0, 'suspended' => 0],
            'subscriptions' => ['total' => 0, 'active' => 0, 'expired' => 0],
            'plans' => ['total' => 0, 'active' => 0],
            'system' => ['status' => 'unhealthy', 'api_connected' => false],
            'recentActivity' => [],
        ];
    }
}

