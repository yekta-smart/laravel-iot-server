<?php

namespace YektaSmart\IotServer\Tests;

use YektaSmart\IotServer\Contracts\IDevice;
use YektaSmart\IotServer\Contracts\IDeviceHandler;

class DummyDeviceHandler implements IDeviceHandler
{
    protected IDevice $device;

    public function getDevice(): IDevice
    {
        return $this->device;
    }
}
