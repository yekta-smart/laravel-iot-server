<?php

namespace YektaSmart\IotServer\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use YektaSmart\IotServer\Models\Frameware;
use YektaSmart\IotServer\Models\FramewareFeature;

/**
 * @extends Factory<FramewareFeature>
 */
class FramewareFeatureFactory extends Factory
{
    protected $model = FramewareFeature::class;

    public function definition()
    {
        return [
            'frameware_id' => Frameware::factory(),
            'name' => fake()->word(),
            'code' => fake()->numberBetween(),
        ];
    }

    public function withFrameware(Frameware|int $frameware): static
    {
        return $this->state(fn () => [
            'frameware' => $frameware,
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
