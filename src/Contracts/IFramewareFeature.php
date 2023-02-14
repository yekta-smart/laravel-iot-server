<?php

namespace YektaSmart\IotServer\Contracts;

interface IFramewareFeature
{
    public function getId(): int;

    public function getFramewareId(): int;

    public function getName(): string;

    public function getCode(): int;

    public function isSoftDeleted(): bool;
}
