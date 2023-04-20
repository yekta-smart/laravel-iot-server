<?php

namespace YektaSmart\IotServer;

use dnj\AAA\Models\User;
use dnj\ErrorTracker\Contracts\IDeviceManager as ContractsIDeviceManager;
use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use YektaSmart\IotServer\Contracts\IDevice;
use YektaSmart\IotServer\Contracts\IDeviceManager;
use YektaSmart\IotServer\Contracts\IFirmware;
use YektaSmart\IotServer\Contracts\IHardware;
use YektaSmart\IotServer\Contracts\IProduct;
use YektaSmart\IotServer\Models\Device;
use YektaSmart\IotServer\Models\Firmware;
use YektaSmart\IotServer\Models\Hardware;
use YektaSmart\IotServer\Models\Product;

class DeviceManager implements IDeviceManager
{
    public function __construct(
        protected ILogger $userLogger,
        protected ContractsIDeviceManager $errorTrackerDeviceManager,
    ) {
    }

    public function find(int $id): ?Device
    {
        return Device::query()->find($id);
    }

    public function findOrFail(int $id): Device
    {
        return Device::query()->findOrFail($id);
    }

    public function findBySerial(string $serial): ?Device
    {
        return Device::query()->where('serial', $serial)->first();
    }

    public function findBySerialOrFail(string $serial): Device
    {
        return Device::query()->where('serial', $serial)->firstOrFail();
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
        int|IFirmware $firmware,
        int|Authenticatable $owner = null,
        array $users = [],
        ?array $historyLimits = null,
        ?array $features = null,
        ?string $serial = null,
        bool $userActivityLog = false,
    ): Device {
        return DB::transaction(function () use ($serial, $title, $product, $hardware, $firmware, $owner, $users, $historyLimits, $features, $userActivityLog) {
            $users = array_map([User::class, 'ensureId'], $users);
            $errorTrackerDevice = $this->errorTrackerDeviceManager->store($title, $owner, null, false);

            /**
             * @var Device
             */
            $device = Device::query()->newModelInstance([
                'serial' => $serial ?? str_replace('-', '', Str::uuid()),
                'title' => $title,
                'product_id' => Product::ensureId($product),
                'hardware_id' => Hardware::ensureId($hardware),
                'firmware_id' => Firmware::ensureId($firmware),
                'history_limits' => $historyLimits,
                'features' => $features,
                'owner_id' => $owner ? User::ensureId($owner) : null,
            ]);
            $device->error_tracker_device_id = $errorTrackerDevice->getId();
            $device->save();
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
            if (array_key_exists('owner', $changes)) {
                $changes['owner_id'] = User::ensureId($changes['owner']);
                unset($changes['owner']);
            }
            foreach (['product', 'hardware', 'firmware'] as $key) {
                if (isset($changes[$key])) {
                    if (is_object($changes[$key])) {
                        if (method_exists($changes[$key], 'getId')) {
                            $changes[$key] = $changes[$key]->getId();
                        } elseif (property_exists($changes[$key], 'id')) {
                            $changes[$key] = $changes[$key]->id;
                        }
                    }
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
                $users = array_map([User::class, 'ensureId'], $changes['users']);
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
     * @return Collection<Firmware>
     */
    public function availableFirmwareUpdate(int|IDevice $device): Collection
    {
        return collect();
    }
}
