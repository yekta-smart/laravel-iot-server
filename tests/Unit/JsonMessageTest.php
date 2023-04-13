<?php

namespace YektaSmart\IotServer\Tests\Unit;

use YektaSmart\IotServer\JsonMessage;
use YektaSmart\IotServer\Tests\TestCase;

class JsonMessageTest extends TestCase
{
    public function test(): void
    {
        $message = new JsonMessage('Error', ['code' => 10, 'message' => 'Internal Error']);
        $this->assertSame('Error', $message->getType());
        $this->assertSame(10, $message->code);
        $this->assertSame('Internal Error', $message->message);

        $this->assertFalse(isset($message->refId));

        $message->refId = '123456789';
        $this->assertSame('123456789', $message->refId);

        $this->assertTrue(isset($message->refId));

        unset($message->refId);
        $this->assertFalse(isset($message->refId));

        $json = $message->serializeToString();
        $this->assertStringContainsString('Internal Error', $json);
        $this->assertSame($json, $message->__toString());

        $this->assertSame([
            '@type' => 'Error',
            'code' => 10,
            'message' => 'Internal Error',
        ], $message->jsonSerialize());

        $message2 = new JsonMessage('');
        $message2->mergeFromString($json);
        $this->assertSame('Error', $message2->getType());
        $this->assertSame(10, $message2->code);
        $this->assertSame('Internal Error', $message2->message);

        $this->expectException(\Exception::class);
        $message2->mergeFromString('{"code": 10, "message": "Internal Error"}');
    }
}
