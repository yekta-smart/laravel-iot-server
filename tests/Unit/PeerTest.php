<?php

namespace YektaSmart\IotServer\Tests\Unit;

use YektaSmart\IotServer\JsonEnvelope;
use YektaSmart\IotServer\Peer;
use YektaSmart\IotServer\Tests\TestCase;

class PeerTest extends TestCase
{
    public function test(): void
    {
        $p = new Peer('1');
        $this->assertSame('1', $p->getId());
        $this->assertFalse($p->hasEnvelopeType());
        $p->setEnvelopeType(JsonEnvelope::class);
        $this->assertTrue($p->hasEnvelopeType());
        $this->assertSame(JsonEnvelope::class, $p->getEnvelopeType());
    }

    public function testGetEnvelopeType(): void
    {
        $p = new Peer('1');
        $this->assertFalse($p->hasEnvelopeType());
        $this->expectException(\Exception::class);
        $p->getEnvelopeType();
    }
}
