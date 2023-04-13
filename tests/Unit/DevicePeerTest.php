<?php

namespace YektaSmart\IotServer\Tests\Unit;

use YektaSmart\IotServer\DevicePeer;
use YektaSmart\IotServer\Tests\TestCase;

class DevicePeerTest extends TestCase
{
    public function test(): void
    {
        $p = new DevicePeer('1', 22);
        $this->assertSame('1', $p->getId());
        $this->assertSame(22, $p->getDeviceId());
    }
}
