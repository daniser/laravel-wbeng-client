<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use TTBooking\WBEngine\Facades\WBEngine;
use TTBooking\WBEngine\WBEngineServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [WBEngineServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'WBEngine' => WBEngine::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        //
    }
}
