<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // CI doesn't run `npm run build`, so there is no Vite manifest.
        $this->withoutVite();
    }
}
