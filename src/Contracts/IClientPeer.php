<?php

namespace YektaSmart\IotServer\Contracts;

use dnj\AAA\Contracts\IUser;

interface IClientPeer extends IPeer
{
    public function getDeviceId(): int;

    public function getUser(): IUser;
}
