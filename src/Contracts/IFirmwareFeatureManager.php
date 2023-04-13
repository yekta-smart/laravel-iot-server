<?php

namespace YektaSmart\IotServer\Contracts;

interface IFirmwareFeatureManager
{
    /**
     * @param array{firmware?:int|IFirmware,name?:string} $filters
     *
     * @return iterable<IFirmwareFeature>
     */
    public function search(array $filters): iterable;

    public function store(int|IFirmware $firmware, string $name, bool $userActivityLog = false): IFirmwareFeature;

    public function trash(int|IFirmwareFeature $feature, bool $userActivityLog = false): IFirmwareFeature;

    public function restore(int|IFirmwareFeature $feature, bool $userActivityLog = false): IFirmwareFeature;

    public function destroy(int|IFirmwareFeature $feature, bool $userActivityLog = false): void;

    /**
     * @return iterable<IFirmwareFeature>
     */
    public function getByFirmware(int|IFirmware $firmware): iterable;

    public function findByCode(int|IFirmware $firmware, int $code): IFirmwareFeature;

    public function findById(int $id): IFirmwareFeature;
}
