<?php

namespace YektaSmart\IotServer\Policies;

use dnj\AAA\Policy;
use YektaSmart\IotServer\Contracts\IHardware;

class HardwarePolicy extends Policy
{
    public function getModel(): string
    {
        return IHardware::class;
    }
}
