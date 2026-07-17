<?php

namespace Tests\Unit\Support;

use App\Support\TenantAppUrl;
use PHPUnit\Framework\TestCase;

class TenantAppUrlTest extends TestCase
{
    public function test_it_removes_whitespace_and_trailing_slashes(): void
    {
        $this->assertSame(
            'http://127.0.0.1:8001',
            TenantAppUrl::normalize(" \thttp://127.0.0.1:8001/// \r\n")
        );
    }
}
