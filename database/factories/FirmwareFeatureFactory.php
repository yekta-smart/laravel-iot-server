<?php

namespace YektaSmart\IotServer\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use YektaSmart\IotServer\Models\Firmware;
use YektaSmart\IotServer\Models\FirmwareFeature;

/**
 * @extends Factory<FirmwareFeature>
 */
class FirmwareFeatureFactory extends Factory
{
    protected $model = FirmwareFeature::class;

    public function definition()
    {
        static $code;
        if (!$code) {
            $code = 0;
        }
        ++$code;

        return [
            'firmware_id' => Firmware::factory(),
            'name' => 'feature_'.$code,
            'code' => $code,
        ];
    }

    public function withFirmware(Firmware|int $firmware): static
    {
        return $this->state(fn () => [
            'firmware_id' => $firmware,
        ]);
    }

    public function withName(string $name): static
    {
        return $this->state(fn () => [
            'name' => $name,
        ]);
    }

    public function withCode(int $code): static
    {
        return $this->state(fn () => [
            'code' => $code,
        ]);
    }
}
