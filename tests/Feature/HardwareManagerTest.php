<?php

namespace YektaSmart\IotServer\Tests\Feature;

use Illuminate\Support\Str;
use YektaSmart\IotServer\Models\Hardware;
use YektaSmart\IotServer\Models\Product;
use YektaSmart\IotServer\Tests\TestCase;

class HardwareManagerTest extends TestCase
{
    public function testFind(): void
    {
        $hardware = Hardware::factory()->create();
        $this->assertNull($this->getHardwareManager()->find(-1));
        $this->assertSame($hardware->getId(), $this->getHardwareManager()->find($hardware->getId())->getId());
        $this->assertSame($hardware->getId(), $this->getHardwareManager()->findOrFail($hardware->getId())->getId());
        $this->assertNull($this->getHardwareManager()->findBySerial(''));
        $this->assertSame($hardware->getId(), $this->getHardwareManager()->findBySerial($hardware->getSerial())->getId());
        $this->assertSame($hardware->getId(), $this->getHardwareManager()->findBySerialOrFail($hardware->getSerial())->getId());
    }

    public function testStore(): void
    {
        $products = Product::factory(2)->create();
        $hardware = $this->getHardwareManager()->store('test', '1.1.1', 1, $products->all(), [], null, true);
        $this->assertSame('test', $hardware->getName());
        $this->assertSame('1.1.1', $hardware->getVersion());
        $this->assertSame(1, $hardware->getOwnerUserId());
        $this->assertSame($products->pluck('id')->all(), $hardware->getProductIds());
        $this->assertEmpty($hardware->getFirmwareIds());
    }

    public function testUpdate(): void
    {
        $hardware = Hardware::factory()->create();
        $newUUID = Str::uuid()->__toString();
        $hardware = $this->getHardwareManager()->update($hardware, [
            'name' => 'newName',
            'version' => '2.0.1',
            'owner' => 2,
            'serial' => $newUUID,
            'products' => [],
            'firmwares' => [],
        ], true);
        $this->assertSame('newName', $hardware->getName());
        $this->assertSame('2.0.1', $hardware->getVersion());
        $this->assertSame(2, $hardware->getOwnerUserId());
        $this->assertSame($newUUID, $hardware->getSerial());
    }

    public function testDestroy(): void
    {
        $hardware = Hardware::factory()->create();
        $this->getHardwareManager()->destroy($hardware, true);
        $this->assertModelMissing($hardware);
    }
}
