<?php

namespace YektaSmart\IotServer\Tests\Feature;

use dnj\Filesystem\Tmp\File;
use Illuminate\Support\Str;
use YektaSmart\IotServer\Models\Firmware;
use YektaSmart\IotServer\Models\Hardware;
use YektaSmart\IotServer\Tests\TestCase;

class FirmwareManagerTest extends TestCase
{
    public function testFind(): void
    {
        $firmware = Firmware::factory()->create();
        $this->assertNull($this->getFirmwareManager()->find(-1));
        $this->assertSame($firmware->getId(), $this->getFirmwareManager()->find($firmware->getId())->getId());
        $this->assertSame($firmware->getId(), $this->getFirmwareManager()->findOrFail($firmware->getId())->getId());

        $this->assertNull($this->getFirmwareManager()->findBySerial(''));
        $this->assertSame($firmware->getId(), $this->getFirmwareManager()->findBySerial($firmware->getSerial())->getId());
        $this->assertSame($firmware->getId(), $this->getFirmwareManager()->findBySerialOrFail($firmware->getSerial())->getId());
    }

    public function testStore(): void
    {
        $hardwares = Hardware::factory(2)->create();
        $file = new File();
        $firmware = $this->getFirmwareManager()->store('test', '1.6.5', $file, ['feature-1', 'feature-2'], $hardwares->all(), 2, null, true);
        $this->assertSame('test', $firmware->getName());
        $this->assertSame('1.6.5', $firmware->getVersion());
        $this->assertSame(get_class($file), get_class($firmware->getFile()));
        $this->assertSame($file->getPath(), $firmware->getFile()->getPath());
        $this->assertEqualsCanonicalizing(['feature-1', 'feature-2'], $firmware->features->pluck('name')->all());
        $this->assertSame(2, $firmware->getOwnerUserId());
        $this->assertSame($hardwares->pluck('id')->all(), $firmware->getHardwareIds());
    }

    public function testUpdate(): void
    {
        $firmware = Firmware::factory()->create();
        $serial = Str::uuid()->__toString();
        $hardwares = Hardware::factory(5)->create();
        $firmware = $this->getFirmwareManager()->update($firmware, [
            'name' => 'newName',
            'version' => '1.7.6',
            'owner' => 1,
            'serial' => $serial,
            'hardwares' => $hardwares->all(),
        ], true);
        $this->assertSame('newName', $firmware->getName());
        $this->assertSame('1.7.6', $firmware->getVersion());
        $this->assertSame(1, $firmware->getOwnerUserId());
        $this->assertSame($serial, $firmware->getSerial());
        $this->assertEqualsCanonicalizing($hardwares->pluck('id')->all(), $firmware->getHardwareIds());
    }

    public function testDestroy(): void
    {
        $firmware = Firmware::factory()->create();
        $this->getFirmwareManager()->destroy($firmware, true);
        $this->assertModelMissing($firmware);
    }
}
