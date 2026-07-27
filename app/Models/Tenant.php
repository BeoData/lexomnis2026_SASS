<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Tenant extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'main_firm_id',
        'tenant_key',
        'db_driver',
        'db_host',
        'db_port',
        'db_name',
        'db_user',
        'db_password',
        'active',
        'sync_status',
        'sync_error',
        'last_synced_at',
        'meta',
    ];

    protected $casts = [
        'active' => 'boolean',
        'last_synced_at' => 'datetime',
        'meta' => 'array',
    ];

    public function getDecryptedPasswordAttribute()
    {
        if (! $this->db_password) {
            return null;
        }

        try {
            return Crypt::decryptString($this->db_password);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
