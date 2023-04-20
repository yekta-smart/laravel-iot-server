<?php

namespace YektaSmart\IotServer\Tests\Feature;

use Illuminate\Support\Str;
use YektaSmart\IotServer\Models\Hardware;
use YektaSmart\IotServer\Models\Product;
use YektaSmart\IotServer\Tests\DummyDeviceHandler;
use YektaSmart\IotServer\Tests\TestCase;

class ProductManagerTest extends TestCase
{
    public function testFind(): void
    {
        $product = Product::factory()->create();
        $this->assertNull($this->getProductManager()->find(-1));
        $this->assertSame($product->getId(), $this->getProductManager()->find($product->getId())->getId());
        $this->assertSame($product->getId(), $this->getProductManager()->findOrFail($product->getId())->getId());
        $this->assertNull($this->getProductManager()->findBySerial(''));
        $this->assertSame($product->getId(), $this->getProductManager()->findBySerial($product->getSerial())->getId());
        $this->assertSame($product->getId(), $this->getProductManager()->findBySerialOrFail($product->getSerial())->getId());
    }

    public function testStore(): void
    {
        $hardwares = Hardware::factory(2)->create();

        $product = $this->getProductManager()->store('test', DummyDeviceHandler::class, 1, $hardwares->all(), [], null, true);
        $this->assertSame('test', $product->getTitle());
        $this->assertSame(DummyDeviceHandler::class, $product->getDeviceHandler());
        $this->assertSame(1, $product->getOwnerUserId());
        $this->assertSame($hardwares->pluck('id')->all(), $product->getHardwareIds());
        $this->assertEmpty($product->getFirmwareIds());
        $this->assertNull($product->getStateHistoryLimits());
    }

    public function testUpdate(): void
    {
        $product = Product::factory()->create();
        $newUUID = str_replace('-', '', Str::uuid()->__toString());
        $product = $this->getProductManager()->update($product, [
            'title' => 'newName',
            'deviceHandler' => DummyDeviceHandler::class,
            'owner' => 2,
            'hardwares' => [],
            'firmwares' => [],
            'stateHistoryLimits' => null,
            'serial' => $newUUID,
        ], true);
        $this->assertSame('newName', $product->getTitle());
        $this->assertSame(DummyDeviceHandler::class, $product->getDeviceHandler());
        $this->assertSame(2, $product->getOwnerUserId());
        $this->assertSame($newUUID, $product->getSerial());
        $this->assertNull($product->getStateHistoryLimits());
    }

    public function testDestroy(): void
    {
        $product = Product::factory()->create();
        $this->getProductManager()->destroy($product, true);
        $this->assertModelMissing($product);
    }
}
