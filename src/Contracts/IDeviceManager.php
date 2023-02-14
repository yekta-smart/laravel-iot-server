<?php

namespace YektaSmart\IotServer\Contracts;

interface IDeviceManager
{
    /**
     * @param array{title?:string,product?:int|IProduct,hardware?:int|IHardware,frameware?:int|IFrameware,owner?:int,userHasAccess?:int} $filters
     *
     * @return iterable<IDevice>
     */
    public function search(array $filters = []): iterable;

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
    ): IDevice;

    /**
     * Only owner can update their's device.
     *
     * @param array{title?:string,product?:int|IProduct,hardware?:int|IHardware,frameware?:int|IFrameware,historyLimits?:array{config?:array{count:int|null,age:int|null},state?:array{count:int|null,age:int|null}}|null,users?:int[],features?:array{enabledIds?:int[],disabledIds?:int[]}|null} $changes
     */
    public function update(
        int|IProduct $product,
        array $changes,
        bool $userActivityLog = false,
    ): void;

    /**
     * Only owner can delete their's device.
     */
    public function destroy(int|IProduct $product, bool $userActivityLog = false): void;

    /**
     * @return iterable<IFrameware>
     */
    public function availableFramewareUpdate(int|IDevice $device): iterable;
}
