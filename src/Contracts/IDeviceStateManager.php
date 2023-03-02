<?php

namespace YektaSmart\IotServer\Contracts;

interface IDeviceStateManager
{
    /**
     * @param array{created_start_date?:\DateTimeInterface,created_end_date?:\DateTimeInterface} $filters
     *
     * @return iterable<IDeviceState>
     */
    public function search(int|IDevice $device, array $filters): iterable;

    public function store(
        int|IDevice $device,
        array $data,
        ?\DateTimeInterface $createdAt = null,
        bool $userActivityLog = false
    ): IDeviceState;

    public function destroy(int|IDeviceState $state, bool $userActivityLog = false): void;
}
