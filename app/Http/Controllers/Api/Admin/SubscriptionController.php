<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        return response()->json(Subscription::with(['firm', 'plan'])->get());
    }

    public function show(Subscription $subscription)
    {
        return response()->json($subscription->load(['firm', 'plan']));
    }
}
