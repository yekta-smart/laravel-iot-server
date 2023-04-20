<?php

namespace YektaSmart\IotServer\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface IProductManager
{
    public function find(int $id): ?IProduct;

    public function findOrFail(int $id): IProduct;

    public function findBySerial(string $serial): ?IProduct;

    public function findBySerialOrFail(string $serial): IProduct;

    /**
     * @param array{title?:string,serial?:string,hardware?:int[],firmware?:int[],owner?:int[]|null,userHasAccess?:int} $filters
     *
     * @return iterable<IProduct>
     */
    public function search(array $filters): iterable;

    /**
     * @param class-string<IDeviceHandler>
     * @param array<int|IHardware>                                               $hardwares
     * @param array<array{id:IFirmware|int,defaultFeatures:int[]}|IFirmware|int> $firmwares
     * @param array{count:int|null,age:int|null}|null                            $stateHistoryLimits
     */
    public function store(
        string $title,
        string $deviceHandler,
        int|Authenticatable $owner,
        array $hardwares = [],
        array $firmwares = [],
        ?array $stateHistoryLimits = null,
        ?string $serial = null,
        bool $userActivityLog = false,
    ): IProduct;

    /**
     * @param array{serial?:string,title?:string,deviceHandler?:class-string<IDeviceHandler>,hardwares?:array<int|IHardware>,firmwares:array<array{id:IFirmware|int,defaultFeatures:int[]}|IFirmware|int>,stateHistoryLimits:array{count:int|null,age:int|null}|null,owner?:int|Authenticatable} $changes
     */
    public function update(int|IProduct $product, array $changes, bool $userActivityLog = false): IProduct;

    public function destroy(int|IProduct $product, bool $userActivityLog = false): void;
}
