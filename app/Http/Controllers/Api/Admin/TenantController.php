<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        return response()->json(Tenant::all());
    }

    public function store(Request $request)
    {
        // Registration logic is usually separate, but for admin API:
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug',
            'email' => 'required|email|unique:tenants,email',
            'status' => 'required|string',
        ]);

        $tenant = Tenant::create($validated);
        return response()->json($tenant, 201);
    }

    public function show(Tenant $tenant)
    {
        return response()->json($tenant);
    }

    public function update(Request $request, Tenant $tenant)
    {
        $tenant->update($request->all());
        return response()->json($tenant);
    }

    public function suspend(Tenant $tenant)
    {
        $tenant->update(['status' => 'suspended']);
        return response()->json($tenant);
    }

    public function activate(Tenant $tenant)
    {
        $tenant->update(['status' => 'active']);
        return response()->json($tenant);
    }
}
