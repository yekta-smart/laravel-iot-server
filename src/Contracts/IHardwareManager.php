<?php

namespace YektaSmart\IotServer\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface IHardwareManager
{
    public function find(int $id): ?IHardware;

    public function findOrFail(int $id): IHardware;

    public function findBySerial(string $serial): ?IHardware;

    public function findBySerialOrFail(string $serial): IHardware;

    /**
     * @param array{owner?:int|null,name?:string,version?:string,compatibleWithProduct?:int|IProduct,compatibleWithFirmware?:int|IFirmware,userHasAccess?:int} $filters
     *
     * @return iterable<IHardware>
     */
    public function search(array $filters): iterable;

    /**
     * @param array<IProduct|int>  $products
     * @param array<IFirmware|int> $firmware
     */
    public function store(
        string $name,
        string $version,
        int|Authenticatable $owner,
        array $products,
        array $firmware,
        ?string $serial = null,
        bool $userActivityLog = false
    ): IHardware;

    /**
     * @param array{name?:string,version?:string,serial?:string,owner?:int|Authenticatable,products?:array<IProduct|int>,firmware?:array<IFirmware|int>}
     */
    public function update(int|IHardware $hardware, array $changes, bool $userActivityLog = false): IHardware;

    public function destroy(int|IHardware $hardware, bool $userActivityLog = false): void;
}
