<?php

namespace YektaSmart\IotServer\Contracts;

use dnj\AAA\Contracts\IOwnerableModel;

interface IHardware extends IOwnerableModel, IHasVersion
{
    public function getID(): int;

    public function getSerial(): string;

    public function getName(): string;

    /**
     * @param int[]
     */
    public function getProductIds(): array;

    /**
     * @param int[]
     */
    public function getFirmwareIds(): array;
}
