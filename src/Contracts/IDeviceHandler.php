<?php

namespace YektaSmart\IotServer\Contracts;

/**
 * Usually uses IDevice,IDeviceConfigManager and IDeviceStateManager by dependecy injector.
 */
interface IDeviceHandler
{
    public function getDevice(): IDevice;
}
