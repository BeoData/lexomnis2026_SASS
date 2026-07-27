<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->unsignedBigInteger('main_firm_id')->nullable()->unique()->after('id');
            $table->string('sync_status')->default('pending')->after('active');
            $table->text('sync_error')->nullable()->after('sync_status');
            $table->timestamp('last_synced_at')->nullable()->after('sync_error');
        });

        Schema::table('tenants_archive', function (Blueprint $table) {
            $table->unsignedBigInteger('main_firm_id')->nullable()->index()->after('id');
            $table->string('sync_status')->nullable()->after('active');
            $table->text('sync_error')->nullable()->after('sync_status');
            $table->timestamp('last_synced_at')->nullable()->after('sync_error');
        });

        DB::table('tenants')->whereNull('main_firm_id')->update([
            'main_firm_id' => DB::raw('id'),
        ]);
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropUnique(['main_firm_id']);
            $table->dropColumn(['main_firm_id', 'sync_status', 'sync_error', 'last_synced_at']);
        });

        Schema::table('tenants_archive', function (Blueprint $table) {
            $table->dropColumn(['main_firm_id', 'sync_status', 'sync_error', 'last_synced_at']);
        });
    }
};
