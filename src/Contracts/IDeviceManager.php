<?php

namespace YektaSmart\IotServer\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface IDeviceManager
{
    public function find(int $id): ?IDevice;

    public function findOrFail(int $id): IDevice;

    public function findBySerial(string $serial): ?IDevice;

    public function findBySerialOrFail(string $serial): IDevice;

    /**
     * @param array{title?:string,product?:int|IProduct,hardware?:int|IHardware,firmware?:int|IFirmware,owner?:int|null,userHasAccess?:int} $filters
     *
     * @return iterable<IDevice>
     */
    public function search(array $filters): iterable;

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
        int|Authenticatable|null $owner = null,
        array $users = [],
        ?array $historyLimits = null,
        ?array $features = null,
        ?string $serial = null,
        bool $userActivityLog = false,
    ): IDevice;

    /**
     * Only owner can update their's device.
     *
     * @param array{serial?:string,title?:string,product?:int|IProduct,hardware?:int|IHardware,firmware?:int|IFirmware,owner?:int|Authenticatable|null,historyLimits?:array{config?:array{count:int|null,age:int|null},state?:array{count:int|null,age:int|null}}|null,users?:int[],features?:array{enabledIds?:int[],disabledIds?:int[]}|null,users?:array<int|Authenticatable>} $changes
     */
    public function update(
        int|IDevice $device,
        array $changes,
        bool $userActivityLog = false,
    ): IDevice;

    /**
     * Only owner can delete their's device.
     */
    public function destroy(int|IDevice $device, bool $userActivityLog = false): void;

    /**
     * @return iterable<IFirmware>
     */
    public function availableFirmwareUpdate(int|IDevice $device): iterable;
}
