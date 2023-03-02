<?php

namespace YektaSmart\IotServer;

use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use YektaSmart\IotServer\Contracts\IDevice;
use YektaSmart\IotServer\Contracts\IDeviceManager;
use YektaSmart\IotServer\Contracts\IFrameware;
use YektaSmart\IotServer\Contracts\IHardware;
use YektaSmart\IotServer\Contracts\IProduct;
use YektaSmart\IotServer\Models\Device;

class DeviceManager implements IDeviceManager
{
    public function __construct(protected ILogger $userLogger)
    {
    }

    /**
     * @return Collection<Device>
     */
    public function search(array $filters): Collection
    {
        return Device::query()->filter($filters)->get();
    }

    /**
     * @param int[]|null                                                                                       $users         additional users who access to this device
     * @param array{config?:array{count:int|null,age:int|null},state?:array{count:int|null,age:int|null}}|null $historyLimits
     * @param array{enabledIds?:int[],disabledIds?:int[]}|null                                                 $features
     */
    public function store(
        string $title,
        int|IProduct $product,
        int|IHardware $hardware,
        int|IFrameware $frameware,
        array $users = [],
        ?array $historyLimits = null,
        ?array $features = null,
        bool $userActivityLog = false,
    ): Device {
        return DB::transaction(function () use ($title, $product, $hardware, $frameware, $users, $historyLimits, $features, $userActivityLog) {
            $users = array_map([UserUtil::class, 'ensureId'], $users);

            /**
             * @var Device
             */
            $device = Device::query()->create([
                'title' => $title,
                'product_id' => $product,
                'hardware' => $hardware,
                'framware' => $frameware,
                'history_limits' => $historyLimits,
                'features' => $features,
            ]);
            $device->users()->sync($users);

            if ($userActivityLog) {
                $this->userLogger->on($device)
                    ->withRequest(request())
                    ->withProperties($device->toArray())
                    ->log('created');
            }

            return $device;
        });
    }

    public function update(
        int|IDevice $device,
        array $changes,
        bool $userActivityLog = false,
    ): Device {
        return DB::transaction(function () use ($device, $changes, $userActivityLog) {
            /**
             * @var Device
             */
            $device = Device::query()
                ->lockForUpdate()
                ->findOrFail(Device::ensureId($device));

            foreach (['product', 'hardware', 'frameware'] as $key) {
                if (isset($changes[$key])) {
                    $changes[$key.'_id'] = $changes[$key];
                    unset($changes[$key]);
                }
            }
            foreach (['historyLimits'] as $key) {
                if (isset($changes[$key])) {
                    $changes[Str::snake($key)] = $changes[$key];
                    unset($changes[$key]);
                }
            }
            if (isset($changes['users'])) {
                $users = array_map([UserUtil::class, 'ensureId'], $changes['users']);
                $device->users()->sync($users);
                unset($changes['users']);
            }
            $device->fill($changes);
            $changes = $device->changesForLog();
            $device->save();
            if ($userActivityLog) {
                $this->userLogger->on($device)
                    ->withRequest(request())
                    ->withProperties($changes)
                    ->log('updated');
            }

            return $device;
        });
    }

    /**
     * Only owner can delete their's device.
     */
    public function destroy(int|IDevice $device, bool $userActivityLog = false): void
    {
        DB::transaction(function () use ($device, $userActivityLog) {
            /**
             * @var Device
             */
            $device = Device::query()
                ->lockForUpdate()
                ->findOrFail(Device::ensureId($device));
            $device->delete();
            if ($userActivityLog) {
                $this->userLogger->on($device)
                    ->withRequest(request())
                    ->withProperties($device->toArray())
                    ->log('destroyed');
            }
        });
    }

    /**
     * @return Collection<Frameware>
     */
    public function availableFramewareUpdate(int|IDevice $device): Collection
    {
        return collect();
    }
}
