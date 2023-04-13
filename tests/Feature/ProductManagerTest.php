<?php

namespace YektaSmart\IotServer\Tests\Feature;

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
        $product = $this->getProductManager()->update($product, [
            'title' => 'newName',
            'deviceHandler' => DummyDeviceHandler::class,
            'owner' => 2,
            'hardwares' => [],
            'firmwares' => [],
            'stateHistoryLimits' => null,
        ], true);
        $this->assertSame('newName', $product->getTitle());
        $this->assertSame(DummyDeviceHandler::class, $product->getDeviceHandler());
        $this->assertSame(2, $product->getOwnerUserId());
        $this->assertNull($product->getStateHistoryLimits());
    }

    public function testDestroy(): void
    {
        $product = Product::factory()->create();
        $this->getProductManager()->destroy($product, true);
        $this->assertModelMissing($product);
    }
}
