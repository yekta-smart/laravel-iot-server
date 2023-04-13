<?php

namespace YektaSmart\IotServer\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface IDeviceConfigManager
{
    /**
     * @param array{configuratorId?:int|null,created_start_date?:\DateTimeInterface,created_end_date?:\DateTimeInterface} $filters
     *
     * @return iterable<IDeviceConfig>
     */
    public function search(int|IDevice $device, array $filters): iterable;

    public function getLatest(int|IDevice $device): ?IDeviceConfig;

    public function store(
        int|IDevice $device,
        array $data,
        int|Authenticatable|null $configuratorId,
        ?array $configuratorData = null,
        ?\DateTimeInterface $createdAt = null,
        bool $userActivityLog = false,
    ): IDeviceConfig;

    public function destroy(int|IDeviceConfig $config, bool $userActivityLog = false): void;
}
