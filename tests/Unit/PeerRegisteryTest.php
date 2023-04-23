<?php

namespace YektaSmart\IotServer\Tests\Unit;

use dnj\AAA\Models\User;
use YektaSmart\IotServer\ClientPeer;
use YektaSmart\IotServer\DevicePeer;
use YektaSmart\IotServer\Peer;
use YektaSmart\IotServer\PeerRegistery;
use YektaSmart\IotServer\Tests\TestCase;

class PeerRegisteryTest extends TestCase
{
    public function testAdd(): void
    {
        $registery = new PeerRegistery();

        $devicePeer = new DevicePeer('1', 2);
        $this->assertFalse($registery->has($devicePeer));
        $this->assertFalse($registery->has('1'));
        $registery->add($devicePeer);
        $this->assertTrue($registery->has($devicePeer));
        $this->assertTrue($registery->has('1'));
        $this->assertTrue($registery->hasDevice(2));

        $user = User::factory()->create();
        $clientPeer = new ClientPeer('4', 2, $user);
        $this->assertFalse($registery->has($clientPeer));
        $this->assertFalse($registery->has('4'));
        $registery->add($clientPeer);
        $this->assertTrue($registery->has($clientPeer));
        $this->assertTrue($registery->has('4'));
        $this->assertTrue($registery->hasClient(2));

        $this->assertNull($registery->firstDevice(10));
        $this->assertNull($registery->firstClient(10));
        $this->assertSame($devicePeer, $registery->firstDevice(2));
        $this->assertSame($clientPeer, $registery->firstClient(2));
        $this->assertEmpty($registery->byDevice(10));
        $this->assertEmpty($registery->getClients(10));
        $this->assertCount(1, $registery->byDevice(2));
        $this->assertCount(1, $registery->getClients(2));

        $this->assertNull($registery->find('10'));
        $this->assertSame($devicePeer, $registery->find('1'));

        $this->expectException(\Exception::class);
        $registery->add($devicePeer);
    }

    public function testRemove(): void
    {
        $registery = new PeerRegistery();

        $peer = new DevicePeer('1', 2);
        $this->assertFalse($registery->has($peer));
        $registery->add($peer);
        $this->assertTrue($registery->has($peer));
        $this->assertTrue($registery->remove('1'));
        $this->assertFalse($registery->has($peer));
        $this->assertFalse($registery->has('1'));
        $this->assertFalse($registery->remove($peer));
    }

    public function testFindOrFail(): void
    {
        $registery = new PeerRegistery();

        $peer = new DevicePeer('1', 2);
        $registery->add($peer);
        $this->assertSame($peer, $registery->findOrFail('1'));

        $this->expectException(\Exception::class);
        $registery->findOrFail('2');
    }

    public function testFirstDeviceOrFail(): void
    {
        $registery = new PeerRegistery();

        $peer = new DevicePeer('1', 2);
        $registery->add($peer);
        $this->assertSame($peer, $registery->firstDeviceOrFail(2));

        $this->expectException(\Exception::class);
        $registery->firstDeviceOrFail(3);
    }

    public function testFirstClientOrFail(): void
    {
        $registery = new PeerRegistery();
        $user = User::factory()->create();
        $peer = new ClientPeer('1', 2, $user);
        $registery->add($peer);
        $this->assertSame($peer, $registery->firstClientOrFail(2));
        $this->assertSame($user, $registery->firstClientOrFail(2)->getUser());

        $this->expectException(\Exception::class);
        $registery->firstClientOrFail(3);
    }

    public function testReplaceTwoUnidenticalPeers(): void
    {
        $registery = new PeerRegistery();

        $peer1 = new DevicePeer('1', 2);
        $peer2 = new DevicePeer('10', 2);

        $this->expectException(\Exception::class);
        $registery->replace($peer1, $peer2);
    }

    public function testReplaceUnpresentCurrentPeer(): void
    {
        $registery = new PeerRegistery();

        $peer1 = new Peer('1');
        $peer2 = new DevicePeer('1', 2);

        $this->expectException(\Exception::class);
        $registery->replace($peer1, $peer2);
    }

    public function testReplace(): void
    {
        $registery = new PeerRegistery();

        $peer1 = new Peer('1');
        $peer2 = new DevicePeer('1', 2);

        $registery->add($peer1);
        $this->assertSame($peer1, $registery->findOrFail('1'));

        $registery->replace($peer1, $peer2);
        $this->assertSame($peer2, $registery->findOrFail('1'));
    }

    public function testAll(): void
    {
        $registery = new PeerRegistery();

        $peer1 = new Peer('1');
        $peer2 = new DevicePeer('2', 2);
        $peer3 = new ClientPeer('3', 2, User::factory()->create());
    

        $registery->add($peer1);
        $registery->add($peer2);
        $registery->add($peer3);
        
        $all = $registery->all();
        $this->assertIsArray($all);
        $this->assertEqualsCanonicalizing([$peer1, $peer2, $peer3], $all);
    }
}
