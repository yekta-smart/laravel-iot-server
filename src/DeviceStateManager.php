<?php

namespace YektaSmart\IotServer;

use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use YektaSmart\IotServer\Contracts\IDevice;
use YektaSmart\IotServer\Contracts\IDeviceState;
use YektaSmart\IotServer\Contracts\IDeviceStateManager;
use YektaSmart\IotServer\Models\Device;
use YektaSmart\IotServer\Models\DeviceState;

class DeviceStateManager implements IDeviceStateManager
{
    public function __construct(protected ILogger $userLogger)
    {
    }

    /**
     * @return Collection<DeviceState>
     */
    public function search(int|IDevice $device, array $filters): Collection
    {
        return DeviceState::query()->forDevice($device)->filter($filters)->get();
    }

    public function getLatest(int|IDevice $device): ?DeviceState
    {
        return DeviceState::query()
            ->forDevice($device)
            ->orderBy('id', 'desc')
            ->first();
    }

    public function store(
        int|IDevice $device,
        array $data,
        ?\DateTimeInterface $createdAt = null,
        bool $userActivityLog = false,
    ): DeviceState {
        return DB::transaction(function () use ($device, $data, $createdAt, $userActivityLog) {
            /**
             * @var DeviceState
             */
            $state = DeviceState::query()->create([
                'device_id' => Device::ensureId($device),
                'created_at' => $createdAt ?? now(),
                'data' => $data,
            ]);

            if ($userActivityLog) {
                $this->userLogger->on($state)
                    ->withRequest(app()->has('request') ? request() : null)
                    ->withProperties($state->toArray())
                    ->log('created');
            }

            return $state;
        });
    }

    public function destroy(int|IDeviceState $state, bool $userActivityLog = false): void
    {
        DB::transaction(function () use ($state, $userActivityLog) {
            /**
             * @var DeviceState
             */
            $state = DeviceState::query()
                ->lockForUpdate()
                ->findOrFail(DeviceState::ensureId($state));
            $state->delete();
            if ($userActivityLog) {
                $this->userLogger->on($state)
                    ->withRequest(app()->has('request') ? request() : null)
                    ->withProperties($state->toArray())
                    ->log('destroyed');
            }
        });
    }
}
