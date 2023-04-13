<?php

namespace YektaSmart\IotServer;

use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use YektaSmart\IotServer\Contracts\IHardware;
use YektaSmart\IotServer\Contracts\IHardwareManager;
use YektaSmart\IotServer\Models\Firmware;
use YektaSmart\IotServer\Models\Hardware;
use YektaSmart\IotServer\Models\Product;

class HardwareManager implements IHardwareManager
{
    public function __construct(protected ILogger $userLogger)
    {
    }

    public function find(int $id): ?Hardware
    {
        return Hardware::query()->find($id);
    }

    public function findOrFail(int $id): Hardware
    {
        return Hardware::query()->findOrFail($id);
    }

    public function findBySerial(string $serial): ?Hardware
    {
        return Hardware::query()->where('serial', $serial)->first();
    }

    public function findBySerialOrFail(string $serial): Hardware
    {
        return Hardware::query()->where('serial', $serial)->firstOrFail();
    }

    /**
     * @return Collection<Hardware>
     */
    public function search(array $filters): Collection
    {
        return Hardware::query()->filter($filters)->get();
    }

    public function store(
        string $name,
        string $version,
        int|Authenticatable $owner,
        array $products,
        array $firmwares,
        ?string $serial = null,
        bool $userActivityLog = false
    ): Hardware {
        return DB::transaction(function () use ($name, $version, $owner, $firmwares, $products, $serial, $userActivityLog) {
            $firmwares = array_map([Firmware::class, 'ensureId'], $firmwares);
            $products = array_map([Product::class, 'ensureId'], $products);
            $owner = UserUtil::ensureId($owner);

            /**
             * @var Hardware
             */
            $hardware = Hardware::query()->create([
                'name' => $name,
                'version' => $version,
                'owner_id' => $owner,
                'serial' => $serial ?? str_replace('-', '', Str::uuid()),
            ]);
            $hardware->products()->sync($products);
            $hardware->firmwares()->sync($firmwares);

            if ($userActivityLog) {
                $this->userLogger->on($hardware)
                    ->withRequest(request())
                    ->withProperties($hardware->toArray())
                    ->log('created');
            }

            return $hardware;
        });
    }

    public function update(int|IHardware $hardware, array $changes, bool $userActivityLog = false): Hardware
    {
        return DB::transaction(function () use ($hardware, $changes, $userActivityLog) {
            /**
             * @var Hardware
             */
            $hardware = Hardware::query()
                ->lockForUpdate()
                ->findOrFail(Hardware::ensureId($hardware));
            if (isset($changes['products'])) {
                $products = array_map([Product::class, 'ensureId'], $changes['products']);
                $hardware->products()->sync($products);
                unset($changes['products']);
            }
            if (isset($changes['firmwares'])) {
                $firmwares = array_map([Firmware::class, 'ensureId'], $changes['firmwares']);
                $hardware->firmwares()->sync($firmwares);
                unset($changes['firmwares']);
            }
            if (isset($changes['owner'])) {
                $changes['owner_id'] = UserUtil::ensureId($changes['owner']);
                unset($changes['owner']);
            }
            $hardware->fill($changes);
            $changes = $hardware->changesForLog();
            $hardware->save();
            if ($userActivityLog) {
                $this->userLogger->on($hardware)
                    ->withRequest(request())
                    ->withProperties($changes)
                    ->log('updated');
            }

            return $hardware;
        });
    }

    public function destroy(int|IHardware $hardware, bool $userActivityLog = false): void
    {
        DB::transaction(function () use ($hardware, $userActivityLog) {
            /**
             * @var Hardware
             */
            $hardware = Hardware::query()
                ->lockForUpdate()
                ->findOrFail(Hardware::ensureId($hardware));
            $hardware->delete();
            if ($userActivityLog) {
                $this->userLogger->on($hardware)
                    ->withRequest(request())
                    ->withProperties($hardware->toArray())
                    ->log('destroyed');
            }
        });
    }
}
