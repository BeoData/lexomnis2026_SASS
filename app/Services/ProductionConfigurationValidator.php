<?php

namespace App\Services;

use RuntimeException;

class ProductionConfigurationValidator
{
    public function validate(): void
    {
        if (! app()->environment('production')) {
            return;
        }

        $url = config('services.tenant_app.url');
        $validated = filter_var($url, FILTER_VALIDATE_URL);
        $host = $validated ? strtolower((string) parse_url($validated, PHP_URL_HOST)) : '';
        if (! $validated || in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
            throw new RuntimeException('TENANT_APP_URL must be a non-local absolute URL in production.');
        }

        if (strlen((string) config('services.tenant_app.api_token')) < 32) {
            throw new RuntimeException('TENANT_APP_API_TOKEN must be configured with at least 32 characters in production.');
        }
    }
}
