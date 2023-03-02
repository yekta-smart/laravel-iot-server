<?php

namespace YektaSmart\IotServer\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use YektaSmart\IotServer\Contracts\IDevice;
use YektaSmart\IotServer\Models\Device;
use YektaSmart\IotServer\Models\DeviceState;

/**
 * @extends Factory<DeviceState>
 */
class DeviceStateFactory extends Factory
{
    protected $model = DeviceState::class;

    public function definition()
    {
        return [
            'device_id' => Device::factory(),
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

    public function withData(?array $data): static
    {
        return $this->state(fn () => [
            'data' => $data,
        ]);
    }
}
