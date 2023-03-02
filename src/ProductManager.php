<?php

namespace YektaSmart\IotServer;

use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use YektaSmart\IotServer\Contracts\IDeviceHandler;
use YektaSmart\IotServer\Contracts\IProduct;
use YektaSmart\IotServer\Contracts\IProductManager;
use YektaSmart\IotServer\Models\Frameware;
use YektaSmart\IotServer\Models\Product;

class ProductManager implements IProductManager
{
    public function __construct(protected ILogger $userLogger)
    {
    }

    /**
     * @return Collection<Product>
     */
    public function search(array $filters): Collection
    {
        return Product::query()->filter($filters)->get();
    }

    public function store(
        string $title,
        string $deviceHandler,
        int|Authenticatable $owner,
        array $hardwares = [],
        array $framewares = [],
        ?array $stateHistoryLimits = null,
        bool $userActivityLog = false,
    ): Product {
        if (!is_subclass_of($deviceHandler, IDeviceHandler::class, true)) {
            throw new \TypeError('device_handler must implemented '.IDeviceHandler::class);
        }

        return DB::transaction(function () use ($title, $deviceHandler, $hardwares, $framewares, $stateHistoryLimits, $owner, $userActivityLog) {
            $framewares = array_map([Frameware::class, 'ensureId'], $framewares);
            $hardwares = array_map([Hardware::class, 'ensureId'], $hardwares);
            $owner = UserUtil::ensureId($owner);

            /**
             * @var Product
             */
            $product = Product::query()->create([
                'title' => $title,
                'device_handler' => $deviceHandler,
                'owner_id' => $owner,
                'state_history_limits' => $stateHistoryLimits,
            ]);
            $product->hardwares()->sync($hardwares);
            $product->framewares()->sync($framewares);

            if ($userActivityLog) {
                $this->userLogger->on($product)
                    ->withRequest(request())
                    ->withProperties($product->toArray())
                    ->log('created');
            }

            return $product;
        });
    }

    /**
     * @param array{title?:string,deviceHandler?:class-string<IDeviceHandler>,hardwares?:array<int|IHardware>,framewares:array<array{id:IFrameware|int,defaultFeatures:int[]}|IFrameware|int>,stateHistoryLimits:array{count:int|null,age:int|null}|null,owner?:int|Authenticatable} $changes
     */
    public function update(int|IProduct $product, array $changes, bool $userActivityLog = false): Product
    {
        return DB::transaction(function () use ($product, $changes, $userActivityLog) {
            /**
             * @var Product
             */
            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail(Product::ensureId($product));

            foreach (['deviceHandler', 'stateHistoryLimits'] as $key) {
                if (isset($changes[$key])) {
                    $changes[Str::snake($key)] = $changes[$key];
                    unset($changes[$key]);
                }
            }

            if (isset($changes['hardwares'])) {
                $hardwares = array_map([Frameware::class, 'ensureId'], $changes['hardwares']);
                $product->hardwares()->sync($hardwares);
                unset($changes['hardwares']);
            }
            if (isset($changes['framewares'])) {
                $framewares = array_map([Frameware::class, 'ensureId'], $changes['framewares']);
                $product->framewares()->sync($framewares);
                unset($changes['framewares']);
            }
            if (isset($changes['owner'])) {
                $changes['owner_id'] = UserUtil::ensureId($changes['owner']);
                unset($changes['owner']);
            }
            $product->fill($changes);
            $changes = $product->changesForLog();
            $product->save();
            if ($userActivityLog) {
                $this->userLogger->on($product)
                    ->withRequest(request())
                    ->withProperties($changes)
                    ->log('updated');
            }
        });
    }

    public function destroy(int|IProduct $hardware, bool $userActivityLog = false): void
    {
        DB::transaction(function ($hardware, $userActivityLog) {
            /**
             * @var Product
             */
            $hardware = Product::query()
                ->lockForUpdate()
                ->findOrFail(Product::ensureId($hardware));
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
