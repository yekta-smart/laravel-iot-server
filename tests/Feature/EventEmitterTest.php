<?php

namespace YektaSmart\IotServer\Tests\Feature;

use YektaSmart\IotServer\EventEmitter;
use YektaSmart\IotServer\Tests\TestCase;

class EventEmitterTest extends TestCase
{
    public function testOn(): void
    {
        $eventData = ['a' => 'b'];
        $hit = 0;
        $emitter = new EventEmitter();

        $this->assertFalse($emitter->emit('event-1', $eventData));

        $emitter->on('event-1', function ($data) use ($eventData, &$hit) {
            $this->assertSame($eventData, $data);
            ++$hit;
        });

        $this->assertTrue($emitter->emit('event-1', $eventData));
        $this->assertTrue($emitter->emit('event-1', $eventData));
        $this->assertSame(2, $hit);
    }

    public function testOnce(): void
    {
        $eventData = ['a' => 'c'];
        $hit = 0;
        $emitter = new EventEmitter();

        $this->assertFalse($emitter->emit('event-1', $eventData));

        $emitter->once('event-1', function ($data) use ($eventData, &$hit) {
            $this->assertSame($eventData, $data);
            ++$hit;
        });

        $this->assertTrue($emitter->emit('event-1', $eventData));
        $this->assertFalse($emitter->emit('event-1', $eventData));
        $this->assertSame(1, $hit);
    }

    public function testOnceWithTimeout(): void
    {
        $hit = 0;
        $emitter = new EventEmitter();
        $this->assertFalse($emitter->emit('event-1'));

        $emitter->once('event-1', function () use (&$hit) { ++$hit; }, time() + 1);
        $emitter->clearExpired();
        $this->assertTrue($emitter->emit('event-1'));
        $this->assertSame(1, $hit);

        $emitter->once('event-1', function () use (&$hit) { ++$hit; }, time() + 1);
        sleep(2);
        $emitter->clearExpired();
        $this->assertFalse($emitter->emit('event-1'));
        $this->assertSame(1, $hit);
    }

    public function testOff(): void
    {
        $eventData = ['a' => 'c'];
        $hit = 0;
        $emitter = new EventEmitter();

        $cb = function ($data) use ($eventData, &$hit) {
            $this->assertSame($eventData, $data);
            ++$hit;
        };
        $emitter->on('event-1', $cb);

        $this->assertTrue($emitter->emit('event-1', $eventData));
        $this->assertSame(1, $hit);

        $emitter->off('event-1', $cb);
        $this->assertFalse($emitter->emit('event-1', $eventData));
        $this->assertSame(1, $hit);

        $emitter->on('event-2', $cb);
        $emitter->on('event-2', function () use (&$hit) {
            ++$hit;
        });
        $this->assertTrue($emitter->emit('event-2', $eventData));
        $this->assertSame(3, $hit);

        $emitter->off('event-2');
        $this->assertFalse($emitter->emit('event-2', $eventData));
        $this->assertSame(3, $hit);
    }
}
