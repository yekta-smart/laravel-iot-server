<?php

namespace YektaSmart\IotServer;

use YektaSmart\IotServer\Contracts\IClientPeer;
use YektaSmart\IotServer\Contracts\IDevice;
use YektaSmart\IotServer\Contracts\IDevicePeer;
use YektaSmart\IotServer\Contracts\IPeer;
use YektaSmart\IotServer\Contracts\IPeerRegistery;
use YektaSmart\IotServer\Models\Device;

class PeerRegistery implements IPeerRegistery
{
    /**
     * @var array<string,IPeer>
     */
    protected array $peers = [];

    public function add(IPeer $peer): void
    {
        if ($this->has($peer)) {
            throw new \Exception('Peer already is present in registry');
        }
        $this->peers[$peer->getId()] = $peer;
    }

    public function replace(IPeer|string $current, IPeer $new): void
    {
        if ($current instanceof IPeer) {
            $current = $current->getId();
        }
        if ($current != $new->getId()) {
            throw new \Exception('peers does not same id');
        }
        if (!$this->has($current)) {
            throw new \Exception('current peer does not present in registry');
        }
        $this->peers[$current] = $new;
    }

    public function firstDevice(int|IDevice $device): ?IDevicePeer
    {
        $device = Device::ensureId($device);
        foreach ($this->peers as $peer) {
            if ($peer instanceof IDevicePeer and $peer->getDeviceId() == $device) {
                return $peer;
            }
        }

        return null;
    }

    public function firstDeviceOrFail(int|IDevice $device): IDevicePeer
    {
        $p = $this->firstDevice($device);
        if (null === $p) {
            throw new \Exception('notfound');
        }

        return $p;
    }

    public function byDevice(int|IDevice $device): array
    {
        $peers = [];
        $device = Device::ensureId($device);
        foreach ($this->peers as $peer) {
            if ($peer instanceof IDevicePeer and $peer->getDeviceId() == $device) {
                $peers[] = $peer;
            }
        }

        return $peers;
    }

    public function hasDevice(int|IDevice $device): bool
    {
        return null !== $this->firstDevice($device);
    }

    public function firstClient(int|IDevice $device): ?ClientPeer
    {
        $device = Device::ensureId($device);
        foreach ($this->peers as $peer) {
            if ($peer instanceof IClientPeer and $peer->getDeviceId() == $device) {
                return $peer;
            }
        }

        return null;
    }

    public function firstClientOrFail(int|IDevice $device): ClientPeer
    {
        $p = $this->firstClient($device);
        if (null === $p) {
            throw new \Exception('notfound');
        }

        return $p;
    }

    /**
     * @return IClientPeer[]
     */
    public function getClients(int|IDevice $device): array
    {
        $peers = [];
        $device = Device::ensureId($device);
        foreach ($this->peers as $peer) {
            if ($peer instanceof IClientPeer and $peer->getDeviceId() == $device) {
                $peers[] = $peer;
            }
        }

        return $peers;
    }

    public function hasClient(int|IDevice $device): bool
    {
        return null !== $this->firstClient($device);
    }

    public function has(IPeer|string $peer): bool
    {
        if ($peer instanceof IPeer) {
            $peer = $peer->getId();
        }

        return isset($this->peers[$peer]);
    }

    public function find(string $peer): ?IPeer
    {
        return $this->peers[$peer] ?? null;
    }

    public function findOrFail(string $peer): IPeer
    {
        if (!isset($this->peers[$peer])) {
            throw new \Exception('Notfound');
        }

        return $this->peers[$peer];
    }

    public function remove(IPeer|string $peer): bool
    {
        if ($peer instanceof IPeer) {
            $peer = $peer->getId();
        }
        if (!$this->has($peer)) {
            return false;
        }
        unset($this->peers[$peer]);

        return true;
    }

    /**
     * @return iterable<IPeer>
     */
    public function all(): iterable
    {
        return $this->peers;
    }
}
