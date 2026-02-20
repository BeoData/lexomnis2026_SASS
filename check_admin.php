<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'superadmin@lexomnis.com';
$user = User::where('email', $email)->first();

if ($user) {
    echo "Superadmin found! ID: " . $user->id . "\n";
    echo "Current Name: " . $user->name . "\n";
    echo "Current Role: " . ($user->role ?? 'N/A') . "\n";
} else {
    echo "Superadmin NOT found. Creating one...\n";
    $user = User::create([
        'name' => 'Super Admin',
        'email' => $email,
        'password' => Hash::make('147852369'),
        'role' => 'super_admin',
    ]);
    echo "Superadmin created with password: 147852369\n";
}
