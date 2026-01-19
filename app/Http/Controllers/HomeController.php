<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // If user is authenticated, redirect to dashboard
        if (Auth::user()) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Home/Index');
    }
}
