<?php

namespace YektaSmart\IotServer;

use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use YektaSmart\IotServer\Contracts\IDevice;
use YektaSmart\IotServer\Contracts\IDeviceConfig;
use YektaSmart\IotServer\Contracts\IDeviceConfigManager;
use YektaSmart\IotServer\Models\Device;
use YektaSmart\IotServer\Models\DeviceConfig;

class DeviceConfigManager implements IDeviceConfigManager
{
    public function __construct(protected ILogger $userLogger)
    {
    }

    /**
     * @return Collection<DeviceConfig>
     */
    public function search(int|IDevice $device, array $filters): Collection
    {
        return DeviceConfig::query()->forDevice($device)->filter($filters)->get();
    }

    public function getLatest(int|IDevice $device): ?DeviceConfig
    {
        return DeviceConfig::query()
            ->forDevice($device)
            ->orderBy('id', 'desc')
            ->first();
    }

    public function store(
        int|IDevice $device,
        array $data,
        int|Authenticatable|null $configuratorId,
        ?array $configuratorData = null,
        ?\DateTimeInterface $createdAt = null,
        bool $userActivityLog = false,
    ): DeviceConfig {
        return DB::transaction(function () use ($device, $data, $createdAt, $configuratorId, $configuratorData, $userActivityLog) {
            if (null === $configuratorId and null === $configuratorData) {
                throw new \InvalidArgumentException('both configuratorData and configuratorId cannot be null');
            }

            /**
             * @var DeviceConfig
             */
            $config = DeviceConfig::query()->create([
                'device_id' => Device::ensureId($device),
                'created_at' => $createdAt ?? now(),
                'data' => $data,
                'configurator_id' => $configuratorId,
                'configurator_data' => $configuratorData,
            ]);

            if ($userActivityLog) {
                $this->userLogger->on($config)
                    ->withRequest(request())
                    ->withProperties($config->toArray())
                    ->log('created');
            }

            return $config;
        });
    }

    public function destroy(int|IDeviceConfig $config, bool $userActivityLog = false): void
    {
        DB::transaction(function () use ($config, $userActivityLog) {
            /**
             * @var DeviceConfig
             */
            $config = DeviceConfig::query()
                ->lockForUpdate()
                ->findOrFail(DeviceConfig::ensureId($config));
            $config->delete();
            if ($userActivityLog) {
                $this->userLogger->on($config)
                    ->withRequest(request())
                    ->withProperties($config->toArray())
                    ->log('destroyed');
            }
        });
    }
}
