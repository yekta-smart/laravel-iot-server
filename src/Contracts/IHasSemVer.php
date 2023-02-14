<?php

namespace YektaSmart\IotServer\Contracts;

interface IHasSemVer
{
    public function getSemVer(): string;

    public function getSemVerInt(): int;
}
