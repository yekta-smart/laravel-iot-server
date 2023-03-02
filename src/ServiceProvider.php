<?php

namespace YektaSmart\IotServer;

use Illuminate\Support\ServiceProvider as SupportServiceProvider;
use YektaSmart\IotServer\Contracts\IDeviceConfigManager;
use YektaSmart\IotServer\Contracts\IDeviceManager;
use YektaSmart\IotServer\Contracts\IDeviceStateManager;
use YektaSmart\IotServer\Contracts\IFolderManager;
use YektaSmart\IotServer\Contracts\IFramewareFeatureManager;
use YektaSmart\IotServer\Contracts\IFramewareManager;
use YektaSmart\IotServer\Contracts\IHardwareManager;
use YektaSmart\IotServer\Contracts\IProductManager;

class ServiceProvider extends SupportServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/iot-server.php', 'iot-server');

        $this->app->singleton(IDeviceManager::class, DeviceManager::class);
        $this->app->singleton(IDeviceConfigManager::class, DeviceConfigManager::class);
        $this->app->singleton(IDeviceStateManager::class, DeviceStateManager::class);
        $this->app->singleton(IFolderManager::class, FolderManager::class);
        $this->app->singleton(IFramewareFeatureManager::class, FramewareFeatureManager::class);
        $this->app->singleton(IFramewareManager::class, FramewareManager::class);
        $this->app->singleton(IProductManager::class, ProductManager::class);
        $this->app->singleton(IHardwareManager::class, HardwareManager::class);
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/iot-server.php' => config_path('iot-server.php'),
            ], 'config');
        }
    }
}
