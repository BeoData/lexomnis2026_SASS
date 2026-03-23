<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // If user is authenticated, redirect based on role
        if (Auth::user()) {
            if (Auth::user()->isSuperAdmin()) {
                return redirect()->route('dashboard');
            }
            return redirect()->route('client.dashboard');
        }

        return Inertia::render('Home/Index');
    }
}
