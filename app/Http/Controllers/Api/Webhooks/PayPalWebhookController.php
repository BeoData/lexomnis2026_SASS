<?php

namespace App\Http\Controllers\Api\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayPalWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('SaaS PayPal Webhook received');
        
        // Stub for now, as delegating to Core App
        return response()->json(['status' => 'success']);
    }
}
