<?php

namespace YektaSmart\IotServer\Tests\Unit;

use YektaSmart\IotServer\JsonEnvelope;
use YektaSmart\IotServer\JsonMessage;
use YektaSmart\IotServer\Tests\TestCase;

class JsonEnvelopeTest extends TestCase
{
    public function test(): void
    {
        $replyTo = 11;
        $message = new JsonMessage('Error', ['code' => 10]);
        $e = JsonEnvelope::newEnvelope($message, $replyTo);

        $this->assertIsInt($e->getId());
        $this->assertSame($replyTo, $e->getReplyTo());
        $this->assertSame($message, $e->getContent());
    }
}
