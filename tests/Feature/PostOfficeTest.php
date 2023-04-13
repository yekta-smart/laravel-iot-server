<?php

namespace YektaSmart\IotServer\Tests\Feature;

use YektaSmart\IotServer\JsonEnvelope;
use YektaSmart\IotServer\JsonMessage;
use YektaSmart\IotServer\Peer;
use YektaSmart\IotServer\PostOffice;
use YektaSmart\IotServer\Tests\TestCase;

class PostOfficeTest extends TestCase
{
    public function testSendAndReceive(): void
    {
        $peer = new Peer(0);
        $peer->setEnvelopeType(JsonEnvelope::class);

        $sendingEnvelope = JsonEnvelope::newEnvelope(new JsonMessage('DeviceState', [
            'sensors' => [
                'temp' => 25.1,
            ],
        ]));

        $postOffice = new PostOffice();
        $replyReceived = false;
        $postOffice->send($peer, $sendingEnvelope, function () use (&$replyReceived) {
            $replyReceived = true;
        });
        $this->assertFalse($replyReceived);

        $peer = new Peer(0);
        $receivingEnvelope = JsonEnvelope::newEnvelope(new JsonMessage('Success', []), $sendingEnvelope->id);
        $postOffice->receive($peer, $receivingEnvelope->serializeToString());
        $this->assertTrue($replyReceived);
    }
}
