<?php

namespace YektaSmart\IotServer\Database\Factories;

use dnj\AAA\Contracts\IUser;
use dnj\AAA\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use YektaSmart\IotServer\Contracts\IDevice;
use YektaSmart\IotServer\Models\Device;
use YektaSmart\IotServer\Models\DeviceConfig;

/**
 * @extends Factory<DeviceConfig>
 */
class DeviceConfigFactory extends Factory
{
    protected $model = DeviceConfig::class;

    public function definition()
    {
        return [
            'device_id' => Device::factory(),
            'configurator_id' => User::factory(),
            'configurator_data' => null,
            'data' => ['key' => 'value1'],
            'created_at' => now(),
        ];
    }

    public function withDevice(int|IDevice $device): static
    {
        return $this->state(fn () => [
            'device_id' => $device,
        ]);
    }

    public function withConfigurator(int|IUser $user): static
    {
        return $this->state(fn () => [
            'configurator_id' => $user,
        ]);
    }

    public function withConfiguratorData(?array $data): static
    {
        return $this->state(fn () => [
            'configurator_data' => $data,
        ]);
    }

    public function withData(?array $data): static
    {
        return $this->state(fn () => [
            'data' => $data,
        ]);
    }
}
