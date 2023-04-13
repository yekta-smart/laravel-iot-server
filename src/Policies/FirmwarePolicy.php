<?php

namespace YektaSmart\IotServer\Policies;

use dnj\AAA\Policy;
use YektaSmart\IotServer\Contracts\IFirmware;

class FirmwarePolicy extends Policy
{
    public function getModel(): string
    {
        return IFirmware::class;
    }
}
