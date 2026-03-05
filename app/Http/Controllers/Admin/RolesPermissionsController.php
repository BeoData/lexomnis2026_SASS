<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantAppApiService;
use Illuminate\Http\Request;

class RolesPermissionsController extends Controller
{
    protected $apiService;

    public function __construct(TenantAppApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index()
    {
        $rolesResponse = $this->apiService->getRoles();
        $permissionsResponse = $this->apiService->getPermissions();

        $roles = $rolesResponse['success'] ? $rolesResponse['data'] : [];
        $modules = $permissionsResponse['success'] ? $permissionsResponse['data'] : [];

        return view('admin.settings.roles-permissions', [
            'roles' => $roles,
            'modules' => $modules,
            'apiError' => (!$rolesResponse['success'] || !$permissionsResponse['success']) 
                ? ($rolesResponse['error'] ?? $permissionsResponse['error'] ?? 'API Error') 
                : null
        ]);
    }
}
