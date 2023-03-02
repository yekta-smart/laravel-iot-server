<?php

namespace YektaSmart\IotServer\Tests;

use dnj\UserLogger\ServiceProvider as UserLoggerServiceProvider;
use dnj\AAA\ServiceProvider as AAAServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use YektaSmart\IotServer\ServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app)
    {
        return [
            UserLoggerServiceProvider::class,
            AAAServiceProvider::class,
            ServiceProvider::class,
        ];
    }
}
