<?php

namespace YektaSmart\IotServer\Contracts;

interface IDeviceConfig
{
    public function getId(): int;

    public function getDeviceId(): int;

    /**
     * @return int|null user id or null if it's done anonymously (e.g. remote manual action)
     */
    public function getConfiguratorId(): ?int;

    /**
     * @return array<string,mixed> additional data about configurator when it's user id is unknown what help the owner found out who reconfigured their's device
     */
    public function getConfiguratorData(): ?array;

    public function getCreatedAt(): \DateTimeInterface;

    /**
     * @return array<string,mixed>
     */
    public function getData(): array;
}
