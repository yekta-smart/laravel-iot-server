<?php

namespace YektaSmart\IotServer\Contracts;

interface IDeviceConfigManager
{
    /**
     * @param array{configuratorId?:int|null,created_start_date?:\DateTimeInterface,created_end_date?:\DateTimeInterface} $filters
     *
     * @return iterable<IDeviceConfig>
     */
    public function search(int|IDevice $device, array $filters = []): iterable;

    public function store(
        int|IDevice $device,
        array $data,
        \DateTimeInterface $createdAt = null,
        int $configuratorId = null,
        array $configuratorData = null,
        bool $userActivityLog = false,
    ): IDeviceConfig;

    public function destroy(int|IDeviceConfig $config, bool $userActivityLog = false): void;
}
