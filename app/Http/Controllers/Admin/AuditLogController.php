<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantAppApiService;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    protected TenantAppApiService $apiService;

    public function __construct(TenantAppApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['action', 'user_id', 'firm_id', 'model_type', 'date_from', 'date_to', 'search']);
        $response = $this->apiService->getAuditLogs($filters);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to fetch audit logs']);
        }

        return view('admin.audit-logs.index', [
            'auditLogs' => $response['data']['data'] ?? [],
            'pagination' => $response['data'] ?? [],
            'filters' => $filters,
        ]);
    }

    public function show(string $id)
    {
        $response = $this->apiService->getAuditLog((int) $id);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Audit log not found']);
        }

        return view('admin.audit-logs.show', [
            'auditLog' => $response['data'] ?? [],
        ]);
    }
}
