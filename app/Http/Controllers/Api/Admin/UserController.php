<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function suspend(User $user)
    {
        $user->update(['status' => 'suspended']);
        return response()->json($user);
    }

    public function forceLogout(User $user)
    {
        return response()->json(['message' => 'User logged out']);
    }

    public function resetPassword(User $user)
    {
        return response()->json(['message' => 'Password reset successful']);
    }

    public function impersonate(User $user)
    {
        return response()->json(['token' => 'dummy-token']);
    }
}
