<?php

namespace YektaSmart\IotServer;

use YektaSmart\IotServer\Contracts\IDevicePeer;

class DevicePeer extends Peer implements IDevicePeer
{
    public function __construct(string $id, protected int $deviceId)
    {
        parent::__construct($id);
    }

    public function getDeviceId(): int
    {
        return $this->deviceId;
    }
}
