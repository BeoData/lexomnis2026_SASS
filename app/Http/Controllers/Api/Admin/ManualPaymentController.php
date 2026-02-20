<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManualPaymentController extends Controller
{
    public function pending()
    {
        return response()->json([]);
    }

    public function approve($id)
    {
        return response()->json(['success' => true]);
    }

    public function reject($id)
    {
        return response()->json(['success' => true]);
    }
}
