<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use TTBooking\WBEngine\Facades;
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
            'WBEngine' => Facades\WBEngine::class,
            'WBSerializer' => Facades\Serializer::class,
            'WBStorage' => Facades\Storage::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        //
    }
}
