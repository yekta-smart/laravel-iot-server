<?php

namespace YektaSmart\IotServer\Contracts;

interface IFirmwareFeature
{
    public function getId(): int;

    public function getFirmwareId(): int;

    public function getName(): string;

    public function getCode(): int;

    public function isSoftDeleted(): bool;
}
