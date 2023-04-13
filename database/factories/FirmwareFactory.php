<?php

namespace YektaSmart\IotServer\Database\Factories;

use dnj\AAA\Contracts\IUser;
use dnj\AAA\Models\User;
use dnj\Filesystem\Contracts\IFile;
use dnj\Filesystem\Tmp\File;
use Illuminate\Database\Eloquent\Factories\Factory;
use YektaSmart\IotServer\Models\Firmware;

/**
 * @extends Factory<Firmware>
 */
class FirmwareFactory extends Factory
{
    protected $model = Firmware::class;

    public function definition()
    {
        return [
            'owner_id' => User::factory(),
            'serial' => str_replace('-', '', fake()->uuid()),
            'name' => fake()->domainName(),
            'file' => new File(),
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

    public function withFile(IFile $file): static
    {
        return $this->state(fn () => [
            'file' => $file,
        ]);
    }
}
