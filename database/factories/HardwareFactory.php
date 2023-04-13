<?php

namespace YektaSmart\IotServer\Database\Factories;

use dnj\AAA\Contracts\IUser;
use dnj\AAA\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use YektaSmart\IotServer\Models\Hardware;

/**
 * @extends Factory<Hardware>
 */
class HardwareFactory extends Factory
{
    protected $model = Hardware::class;

    public function definition()
    {
        return [
            'owner_id' => User::factory(),
            'serial' => str_replace('-', '', fake()->uuid()),
            'name' => fake()->domainName(),
            'version' => fake()->semver(false, false),
        ];
    }

    public function withOwner(int|IUser $owner): static
    {
        return $this->state(fn () => [
            'owner_id' => $owner,
        ]);
    }

    public function withName(string $name): static
    {
        return $this->state(fn () => [
            'name' => $name,
        ]);
    }

    public function withVersion(int $version): static
    {
        return $this->state(fn () => [
            'version' => $version,
        ]);
    }
}
