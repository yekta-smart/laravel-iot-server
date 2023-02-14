<?php

namespace YektaSmart\IotServer\Contracts;

use dnj\AAA\Contracts\IOwnerableModel;

interface IHardware extends IOwnerableModel, IHasSemVer
{
    public function getID(): int;

    public function getName(): string;

    /**
     * @param int[]
     */
    public function getProductIds(): array;

    /**
     * @param int[]
     */
    public function getFramewareIds(): array;
}
