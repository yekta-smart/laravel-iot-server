<?php

namespace YektaSmart\IotServer;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as SupportServiceProvider;
use YektaSmart\IotServer\Contracts\IDevice;
use YektaSmart\IotServer\Contracts\IDeviceConfigManager;
use YektaSmart\IotServer\Contracts\IDeviceManager;
use YektaSmart\IotServer\Contracts\IDeviceStateManager;
use YektaSmart\IotServer\Contracts\IEnvelope;
use YektaSmart\IotServer\Contracts\IFirmware;
use YektaSmart\IotServer\Contracts\IFirmwareFeatureManager;
use YektaSmart\IotServer\Contracts\IFirmwareManager;
use YektaSmart\IotServer\Contracts\IFolderManager;
use YektaSmart\IotServer\Contracts\IHardware;
use YektaSmart\IotServer\Contracts\IHardwareManager;
use YektaSmart\IotServer\Contracts\IPeerRegister;
use YektaSmart\IotServer\Contracts\IPeerRegistery;
use YektaSmart\IotServer\Contracts\IPostOffice;
use YektaSmart\IotServer\Contracts\IProduct;
use YektaSmart\IotServer\Contracts\IProductManager;
use YektaSmart\IotServer\Policies\DevicePolicy;
use YektaSmart\IotServer\Policies\FirmwarePolicy;
use YektaSmart\IotServer\Policies\HardwarePolicy;
use YektaSmart\IotServer\Policies\ProductPolicy;

class ServiceProvider extends SupportServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected $policies = [
        IDevice::class => DevicePolicy::class,
        IHardware::class => HardwarePolicy::class,
        IFirmware::class => FirmwarePolicy::class,
        IProduct::class => ProductPolicy::class,
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/iot-server.php', 'iot-server');
        $this->app->singleton(IDeviceManager::class, DeviceManager::class);
        $this->app->singleton(IDeviceConfigManager::class, DeviceConfigManager::class);
        $this->app->singleton(IDeviceStateManager::class, DeviceStateManager::class);
        $this->app->singleton(IFolderManager::class, FolderManager::class);
        $this->app->singleton(IFirmwareFeatureManager::class, FirmwareFeatureManager::class);
        $this->app->singleton(IFirmwareManager::class, FirmwareManager::class);
        $this->app->singleton(IProductManager::class, ProductManager::class);
        $this->app->singleton(IHardwareManager::class, HardwareManager::class);
        $this->app->singleton(IPeerRegister::class, PeerRegister::class);
        $this->app->singleton(IPostOffice::class, PostOffice::class);
        $this->app->singleton(IPeerRegistery::class, PeerRegistery::class);
        $this->app->tag([JsonEnvelope::class], IEnvelope::class);
    }

    public function boot(): void
    {
        $this->loadRoutes();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->registerPolicies();
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/iot-server.php' => config_path('iot-server.php'),
            ], 'config');
        }
    }

    public function registerPolicies(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    protected function loadRoutes(): void
    {
        if (config('iot-server.routes.enable')) {
            Route::prefix(config('iot-server.routes.prefix'))->group(function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
            });
        }
    }
}
