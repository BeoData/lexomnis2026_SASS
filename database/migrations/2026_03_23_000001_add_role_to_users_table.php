<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('client')->after('email');
        });

        // Set existing users without a role to superadmin (they were created before roles existed)
        \App\Models\User::where('role', 'client')
            ->where('email', 'superadmin@lexomnis.com')
            ->update(['role' => 'superadmin']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
