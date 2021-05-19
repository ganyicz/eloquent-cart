<?php

namespace Ganyicz\Cart\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Ganyicz\Cart\CartServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            CartServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
