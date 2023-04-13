<?php

namespace YektaSmart\IotServer\Contracts;

interface IDevicePeer extends IPeer
{
    public function getDeviceId(): int;
}
