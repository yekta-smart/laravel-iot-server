<?php

namespace YektaSmart\IotServer\Database\Factories;

use dnj\AAA\Contracts\IUser;
use dnj\AAA\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use YektaSmart\IotServer\Contracts\IFolder;
use YektaSmart\IotServer\Models\Folder;

/**
 * @extends Factory<Folder>
 */
class FolderFactory extends Factory
{
    protected $model = Folder::class;

    public function definition()
    {
        return [
            'parent_id' => null,
            'owner_id' => User::factory(),
            'title' => fake()->word(),
        ];
    }

    public function withParnet(int|IFolder $parent): static
    {
        return $this->state(fn () => [
            'parent_id' => $parent,
        ]);
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
}
