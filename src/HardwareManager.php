<?php

namespace YektaSmart\IotServer;

use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use YektaSmart\IotServer\Contracts\IHardware;
use YektaSmart\IotServer\Contracts\IHardwareManager;
use YektaSmart\IotServer\Models\Frameware;
use YektaSmart\IotServer\Models\Hardware;
use YektaSmart\IotServer\Models\Product;

class HardwareManager implements IHardwareManager
{
    public function __construct(protected ILogger $userLogger)
    {
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
        array $framewares,
        bool $userActivityLog = false
    ): Hardware {
        return DB::transaction(function () use ($name, $version, $owner, $framewares, $products, $userActivityLog) {
            $framewares = array_map([Frameware::class, 'ensureId'], $framewares);
            $products = array_map([Product::class, 'ensureId'], $products);
            $owner = UserUtil::ensureId($owner);

            /**
             * @var Hardware
             */
            $hardware = Hardware::query()->create([
                'name' => $name,
                'version' => $version,
                'owner_id' => $owner,
            ]);
            $hardware->products()->sync($products);
            $hardware->framewares()->sync($framewares);

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
            if (isset($changes['framewares'])) {
                $framewares = array_map([Frameware::class, 'ensureId'], $changes['framewares']);
                $hardware->framewares()->sync($framewares);
                unset($changes['framewares']);
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
        });
    }

    public function destroy(int|IHardware $hardware, bool $userActivityLog = false): void
    {
        DB::transaction(function ($hardware, $userActivityLog) {
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
