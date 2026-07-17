<?php

namespace App\Support;

final class TenantAppUrl
{
    public static function normalize(?string $url): string
    {
        return rtrim(trim((string) $url), '/');
    }
}
