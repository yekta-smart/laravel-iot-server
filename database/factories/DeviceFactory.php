<?php

namespace YektaSmart\IotServer\Database\Factories;

use dnj\AAA\Contracts\IUser;
use dnj\AAA\Models\User;
use dnj\ErrorTracker\Laravel\Server\Models\Device as ErrorTrackerDevice;
use Illuminate\Database\Eloquent\Factories\Factory;
use YektaSmart\IotServer\Contracts\IFirmware;
use YektaSmart\IotServer\Contracts\IHardware;
use YektaSmart\IotServer\Contracts\IProduct;
use YektaSmart\IotServer\Models\Device;
use YektaSmart\IotServer\Models\Firmware;
use YektaSmart\IotServer\Models\Hardware;
use YektaSmart\IotServer\Models\Product;

/**
 * @extends Factory<Device>
 */
class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition()
    {
        return [
            'owner_id' => User::factory(),
            'serial' => str_replace('-', '', fake()->uuid()),
            'title' => fake()->words(3, true),
            'product_id' => Product::factory(),
            'hardware_id' => Hardware::factory(),
            'firmware_id' => Firmware::factory(),
            'history_limits' => null,
            'features' => null,
            'error_tracker_device_id' => ErrorTrackerDevice::factory(),
        ];
    }

    public function withOwner(int|IUser $owner): static
    {
        return $this->state(fn () => [
            'owner_id' => $owner,
        ]);
    }

    public function withTitle(string $title): static
    {
        return $this->state(fn () => [
            'title' => $title,
        ]);
    }

    public function withProduct(int|IProduct $product): static
    {
        return $this->state(fn () => [
            'product_id' => $product,
        ]);
    }

    public function withHardware(int|IHardware $hardware): static
    {
        return $this->state(fn () => [
            'hardware_id' => $hardware,
        ]);
    }

    public function withFirmware(int|IFirmware $firmware): static
    {
        return $this->state(fn () => [
            'firmware_id' => $firmware,
        ]);
    }

    public function withHistoryLimits(array $historyLimits): static
    {
        return $this->state(fn () => [
            'history_limits' => $historyLimits,
        ]);
    }

    public function withFeatures(array $features): static
    {
        return $this->state(fn () => [
            'features' => $features,
        ]);
    }

    public function withErrorTrackerDevice(ErrorTrackerDevice $device): static
    {
        return $this->state(fn () => [
            'error_tracker_device_id' => $device,
        ]);
    }
}
