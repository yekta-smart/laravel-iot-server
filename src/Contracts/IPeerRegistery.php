<?php

namespace YektaSmart\IotServer\Contracts;

interface IPeerRegistery
{
    public function add(IPeer $peer): void;

    public function replace(IPeer|string $current, IPeer $new): void;

    public function firstDevice(int|IDevice $device): ?IDevicePeer;

    public function firstDeviceOrFail(int|IDevice $device): IDevicePeer;

    /**
     * @return IDevicePeer[]
     */
    public function byDevice(int|IDevice $device): array;

    public function hasDevice(int|IDevice $device): bool;

    public function firstClient(int|IDevice $device): ?IClientPeer;

    public function firstClientOrFail(int|IDevice $device): IClientPeer;

    /**
     * @return IClientPeer[]
     */
    public function getClients(int|IDevice $device): array;

    public function hasClient(int|IDevice $device): bool;

    public function has(IPeer|string $peer): bool;

    public function remove(IPeer|string $peer): bool;

    public function find(string $id): ?IPeer;

    public function findOrFail(string $id): IPeer;

    /**
     * @return iterable<IPeer>
     */
    public function all(): iterable;
}
