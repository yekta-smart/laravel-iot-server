<?php

namespace YektaSmart\IotServer\Tests;

use dnj\AAA\Contracts\IUser;
use dnj\AAA\Models\Type;
use dnj\AAA\Models\TypeAbility;
use dnj\AAA\Models\User;
use dnj\AAA\Policy;
use dnj\AAA\ServiceProvider as AAAServiceProvider;
use dnj\ErrorTracker\Laravel\Server\ServiceProvider as ErrorTrackerServerServiceProvider;
use dnj\UserLogger\ServiceProvider as UserLoggerServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use YektaSmart\IotServer\Contracts\IDeviceManager;
use YektaSmart\IotServer\Contracts\IFirmwareFeatureManager;
use YektaSmart\IotServer\Contracts\IFirmwareManager;
use YektaSmart\IotServer\Contracts\IHardwareManager;
use YektaSmart\IotServer\Contracts\IProductManager;
use YektaSmart\IotServer\DeviceManager;
use YektaSmart\IotServer\FirmwareFeatureManager;
use YektaSmart\IotServer\FirmwareManager;
use YektaSmart\IotServer\HardwareManager;
use YektaSmart\IotServer\ProductManager;
use YektaSmart\IotServer\ServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    public function getHardwareManager(): HardwareManager
    {
        return $this->app->make(IHardwareManager::class);
    }

    public function getProductManager(): ProductManager
    {
        return $this->app->make(IProductManager::class);
    }

    public function getFirmwareManager(): FirmwareManager
    {
        return $this->app->make(IFirmwareManager::class);
    }

    public function getFirmwareFeatureManager(): FirmwareFeatureManager
    {
        return $this->app->make(IFirmwareFeatureManager::class);
    }

    public function getDeviceManager(): DeviceManager
    {
        return $this->app->make(IDeviceManager::class);
    }

    protected function createUserWithAbility(string $ability): IUser
    {
        $myType = Type::factory()
            ->has(TypeAbility::factory()->withName($ability), 'abilities')
            ->create();

        return User::factory()->withType($myType)->create();
    }

    protected function createUserWithModelAbility(string $model, string $ability): IUser
    {
        return $this->createUserWithAbility(Policy::getModelAbilityName($model, $ability));
    }

    protected function getPackageProviders($app)
    {
        return [
            UserLoggerServiceProvider::class,
            AAAServiceProvider::class,
            ErrorTrackerServerServiceProvider::class,
            ServiceProvider::class,
        ];
    }
}
