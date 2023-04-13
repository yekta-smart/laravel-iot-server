<?php

namespace YektaSmart\IotServer;

use dnj\AAA\Models\User;
use dnj\ErrorTracker\Contracts\IAppManager;
use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use YektaSmart\IotServer\Contracts\IDeviceHandler;
use YektaSmart\IotServer\Contracts\IProduct;
use YektaSmart\IotServer\Contracts\IProductManager;
use YektaSmart\IotServer\Models\Firmware;
use YektaSmart\IotServer\Models\Hardware;
use YektaSmart\IotServer\Models\Product;

class ProductManager implements IProductManager
{
    public function __construct(
        protected ILogger $userLogger,
        protected IAppManager $appManager,
    ) {
    }

    public function find(int $id): ?Product
    {
        return Product::query()->find($id);
    }

    public function findOrFail(int $id): Product
    {
        return Product::query()->findOrFail($id);
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
        array $firmwares = [],
        ?array $stateHistoryLimits = null,
        bool $userActivityLog = false,
    ): Product {
        if (!is_subclass_of($deviceHandler, IDeviceHandler::class, true)) {
            throw new \TypeError('device_handler must implemented '.IDeviceHandler::class);
        }

        return DB::transaction(function () use ($title, $deviceHandler, $hardwares, $firmwares, $stateHistoryLimits, $owner, $userActivityLog) {
            $firmwares = array_map([Firmware::class, 'ensureId'], $firmwares);
            $hardwares = array_map([Hardware::class, 'ensureId'], $hardwares);
            $owner = User::ensureId($owner);

            $app = $this->appManager->store($title, $owner, null, false);
            /**
             * @var Product
             */
            $product = Product::query()->newModelInstance([
                'title' => $title,
                'device_handler' => $deviceHandler,
                'owner_id' => $owner,
                'state_history_limits' => $stateHistoryLimits,
            ]);
            $product->error_tracker_app_id = $app;
            $product->save();
            $product->hardwares()->sync($hardwares);
            $product->firmwares()->sync($firmwares);

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
     * @param array{title?:string,deviceHandler?:class-string<IDeviceHandler>,hardwares?:array<int|IHardware>,firmwares:array<array{id:IFirmware|int,defaultFeatures:int[]}|IFirmware|int>,stateHistoryLimits:array{count:int|null,age:int|null}|null,owner?:int|Authenticatable} $changes
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
                $hardwares = array_map([Firmware::class, 'ensureId'], $changes['hardwares']);
                $product->hardwares()->sync($hardwares);
                unset($changes['hardwares']);
            }
            if (isset($changes['firmwares'])) {
                $firmwares = array_map([Firmware::class, 'ensureId'], $changes['firmwares']);
                $product->firmwares()->sync($firmwares);
                unset($changes['firmwares']);
            }
            if (isset($changes['owner'])) {
                $changes['owner_id'] = User::ensureId($changes['owner']);
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

            return $product;
        });
    }

    public function destroy(int|IProduct $product, bool $userActivityLog = false): void
    {
        DB::transaction(function () use ($product, $userActivityLog) {
            /**
             * @var Product
             */
            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail(Product::ensureId($product));
            $product->delete();
            if ($userActivityLog) {
                $this->userLogger->on($product)
                    ->withRequest(request())
                    ->withProperties($product->toArray())
                    ->log('destroyed');
            }
        });
    }
}
