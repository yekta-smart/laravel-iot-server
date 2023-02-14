<?php

namespace YektaSmart\IotServer\Contracts;

interface IDeviceState
{
    public function getId(): int;

    public function getDeviceId(): int;

    public function getCreatedAt(): \DateTimeInterface;

    /**
     * @return array<string,mixed>
     */
    public function getData(): array;
}
