<?php

namespace YektaSmart\IotServer\Contracts;

use dnj\Filesystem\Contracts\IFile;
use Illuminate\Contracts\Auth\Authenticatable;

interface IFirmwareManager
{
    public function find(int $id): ?IFirmware;

    public function findOrFail(int $id): IFirmware;

    public function findBySerial(string $serial): ?IFirmware;

    public function findBySerialOrFail(string $serial): IFirmware;

    /**
     * @param array{owner?:int|null,name?:string,compatibleWithHardware?:int,userHasAccess?:int} $filters
     *
     * @return iterable<IFirmware>
     */
    public function search(array $filters): iterable;

    /**
     * @param string[]             $features
     * @param array<IHardware|int> $hardwares
     */
    public function store(
        string $name,
        string $version,
        IFile $file,
        array $features,
        array $hardwares,
        int|Authenticatable $owner,
        ?string $serial = null,
        bool $userActivityLog = false,
    ): IFirmware;

    /**
     * @param array{name?:string,version?:string,file?:IFile,serial?:string,hardwares?:array<IHardware|int>,owner?:int|Authenticatable} $changes
     */
    public function update(int|IFirmware $firmware, array $changes, bool $userActivityLog = false): IFirmware;

    public function destroy(int|IFirmware $firmware, bool $userActivityLog = false): void;
}
