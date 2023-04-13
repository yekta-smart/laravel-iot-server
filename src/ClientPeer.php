<?php

namespace YektaSmart\IotServer;

use dnj\AAA\Contracts\IUser;
use YektaSmart\IotServer\Contracts\IClientPeer;

class ClientPeer extends Peer implements IClientPeer
{
    public function __construct(
        string $id,
        protected int $deviceId,
        protected IUser $user,
    ) {
        parent::__construct($id);
    }

    public function getDeviceId(): int
    {
        return $this->deviceId;
    }

    public function getUser(): IUser
    {
        return $this->user;
    }
}
