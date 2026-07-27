<?php

namespace Tests\Unit\Services;

use App\Services\ProductionConfigurationValidator;
use RuntimeException;
use Tests\TestCase;

class ProductionConfigurationValidatorTest extends TestCase
{
    public function test_production_rejects_local_tenant_app_url(): void
    {
        $this->app['env'] = 'production';
        config([
            'services.tenant_app.url' => 'http://127.0.0.1:8001',
            'services.tenant_app.api_token' => str_repeat('x', 64),
        ]);

        $this->expectException(RuntimeException::class);
        app(ProductionConfigurationValidator::class)->validate();
    }

    public function test_local_environment_keeps_development_defaults_usable(): void
    {
        config([
            'services.tenant_app.url' => 'http://localhost:8000',
            'services.tenant_app.api_token' => null,
        ]);

        app(ProductionConfigurationValidator::class)->validate();
        $this->addToAssertionCount(1);
    }
}
