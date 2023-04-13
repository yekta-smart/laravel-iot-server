<?php

namespace YektaSmart\IotServer\Contracts;

interface IHasVersion
{
    public function getVersion(): string;

    public function getVersionInt(): int;
}
