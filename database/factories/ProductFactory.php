<?php

namespace YektaSmart\IotServer\Database\Factories;

use dnj\AAA\Contracts\IUser;
use dnj\AAA\Models\User;
use dnj\ErrorTracker\Contracts\IApp;
use dnj\ErrorTracker\Laravel\Server\Models\App;
use Illuminate\Database\Eloquent\Factories\Factory;
use YektaSmart\IotServer\Models\Product;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'serial' => str_replace('-', '', fake()->uuid()),
            'owner_id' => User::factory(),
            'title' => fake()->word(),
            'device_handler' => '',
            'state_history_limits' => null,
            'error_tracker_app_id' => App::factory(),
        ];
    }

    public function withSerial(string $serial): static
    {
        return $this->state(fn () => [
            'serial' => $serial,
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

    public function withDeviceHandler(string $deviceHandler): static
    {
        return $this->state(fn () => [
            'device_handler' => $deviceHandler,
        ]);
    }

    public function withStateHistoryLimits(?array $limits): static
    {
        return $this->state(fn () => [
            'state_history_limits' => $limits,
        ]);
    }

    public function withErrorTrackerApp(int|IApp $app): static
    {
        return $this->state(fn () => [
            'error_tracker_app_id' => $app,
        ]);
    }

    /**
     * @param array<int,int[]>|null $features
     */
    public function withFeatures(?array $features): static
    {
        return $this->state(fn () => [
            'features' => $features,
        ]);
    }
}
