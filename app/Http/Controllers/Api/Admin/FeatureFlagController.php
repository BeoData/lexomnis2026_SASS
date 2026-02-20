<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeatureFlagController extends Controller
{
    public function index()
    {
        return response()->json([]);
    }

    public function getTenantFlags($tenantId)
    {
        return response()->json([]);
    }
}
