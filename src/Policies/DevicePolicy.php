<?php

namespace YektaSmart\IotServer\Policies;

use dnj\AAA\Policy;
use YektaSmart\IotServer\Contracts\IDevice;

class DevicePolicy extends Policy
{
    public function getModel(): string
    {
        return IDevice::class;
    }
}
