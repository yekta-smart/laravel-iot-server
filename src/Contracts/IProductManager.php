<?php

namespace YektaSmart\IotServer\Contracts;

interface IProductManager
{
    /**
     * @param array{title?:string,hardware?:int[],frameware?:int[],owner?:int[]|null} $filters
     *
     * @return iterable<IProduct>
     */
    public function search(array $filters = []): iterable;

    /**
     * @param class-string<IDeviceHandler>
     * @param array<int|IHardware>                                                 $hardwares
     * @param array<array{id:IFrameware|int,defaultFeatures:int[]}|IFrameware|int> $framewares
     * @param array{count:int|null,age:int|null}|null                              $stateHistoryLimits
     */
    public function store(
        string $title,
        string $deviceHandler,
        array $hardwares = [],
        array $framewares = [],
        ?array $stateHistoryLimits = null,
        ?int $owner = null,
        bool $userActivityLog = false,
    ): IProduct;

    /**
     * @param array{title?:string,deviceHandler?:class-string<IDeviceHandler>,hardwares?:array<int|IHardware>,framewares:array<array{id:IFrameware|int,defaultFeatures:int[]}|IFrameware|int>,stateHistoryLimits:array{count:int|null,age:int|null}|null,owner?:int|null} $changes
     */
    public function update(int|IProduct $product, array $changes, bool $userActivityLog = false): IProduct;

    public function destroy(int|IProduct $product, bool $userActivityLog = false): void;
}
