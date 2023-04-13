<?php

namespace YektaSmart\IotServer\Tests\Unit;

use YektaSmart\IotServer\Casts\SemVer;
use YektaSmart\IotServer\Tests\TestCase;

class SemVerCastTest extends TestCase
{
    public function test(): void
    {
        $cast = new SemVer();
        $this->assertSame(0b00000000000100000000010000000001, $cast->set(null, 'version', '1.1.1', []));
        $this->assertSame(0b00000000000100000000000000000001, $cast->set(null, 'version', '1.0.1', []));
        $this->assertSame(0b00000000001000000000010000000001, $cast->set(null, 'version', '2.1.1', []));
        $this->assertSame('2.1.1', $cast->get(null, 'version', 0b00000000001000000000010000000001, []));
        $this->assertSame('3.1.5', $cast->get(null, 'version', 0b00000000001100000000010000000101, []));
    }
}
