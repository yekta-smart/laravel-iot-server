<?php

namespace YektaSmart\IotServer\Database\Factories;

use dnj\AAA\Contracts\IUser;
use dnj\AAA\Models\User;
use dnj\Filesystem\Contracts\IFile;
use dnj\Filesystem\Tmp\File;
use Illuminate\Database\Eloquent\Factories\Factory;
use YektaSmart\IotServer\Models\Frameware;

/**
 * @extends Factory<Frameware>
 */
class FramewareFactory extends Factory
{
    protected $model = Frameware::class;

    public function definition()
    {
        return [
            'owner_id' => User::factory(),
            'name' => fake()->domainName(),
            'file' => new File(),
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
