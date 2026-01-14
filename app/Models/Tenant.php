<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'tenant_key',
        'db_driver',
        'db_host',
        'db_port',
        'db_name',
        'db_user',
        'db_password',
        'active',
        'meta',
    ];

    protected $casts = [
        'active' => 'boolean',
        'meta' => 'array',
    ];

    public function getDecryptedPasswordAttribute()
    {
        if (!$this->db_password) {
            return null;
        }

        try {
            return Crypt::decryptString($this->db_password);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
