<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        return response()->json(Plan::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:plans,slug',
            'description' => 'nullable|string',
            'monthly_price' => 'required|numeric',
            'yearly_price' => 'required|numeric',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $plan = Plan::create($validated);
        return response()->json($plan, 201);
    }

    public function show(Plan $plan)
    {
        return response()->json($plan);
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:plans,slug,' . $plan->id,
            'description' => 'nullable|string',
            'monthly_price' => 'sometimes|required|numeric',
            'yearly_price' => 'sometimes|required|numeric',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $plan->update($validated);
        return response()->json($plan);
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return response()->json(null, 204);
    }
}
