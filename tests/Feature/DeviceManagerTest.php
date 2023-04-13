<?php

namespace YektaSmart\IotServer\Tests\Feature;

use dnj\AAA\Models\User;
use Illuminate\Support\Str;
use YektaSmart\IotServer\Models\Device;
use YektaSmart\IotServer\Models\Firmware;
use YektaSmart\IotServer\Models\Hardware;
use YektaSmart\IotServer\Models\Product;
use YektaSmart\IotServer\Tests\TestCase;

class DeviceManagerTest extends TestCase
{
    public function testFind(): void
    {
        $device = Device::factory()->create();
        $this->assertNull($this->getDeviceManager()->find(-1));
        $this->assertSame($device->getId(), $this->getDeviceManager()->find($device->getId())->getId());
        $this->assertSame($device->getId(), $this->getDeviceManager()->findOrFail($device->getId())->getId());
        $this->assertNull($this->getDeviceManager()->findBySerial(''));
        $this->assertSame($device->getId(), $this->getDeviceManager()->findBySerial($device->getSerial())->getId());
        $this->assertSame($device->getId(), $this->getDeviceManager()->findBySerialOrFail($device->getSerial())->getId());
    }

    public function testStore(): void
    {
        $product = Product::factory()->create();
        $hardware = Hardware::factory()->create();
        $firmware = Firmware::factory()->create();
        $users = User::factory(3)->create();
        $serial = Str::uuid()->__toString();
        $owner = User::factory()->create();
        $device = $this->getDeviceManager()->store(
            'test',
            $product,
            $hardware,
            $firmware,
            $owner,
            $users->all(),
            null,
            null,
            $serial,
            true
        );
        $this->assertSame('test', $device->getTitle());
        $this->assertSame($product->getId(), $device->getProductId());
        $this->assertSame($hardware->getId(), $device->getHardwareId());
        $this->assertSame($firmware->getId(), $device->getFirmwareId());
        $this->assertEqualsCanonicalizing($users->pluck('id')->all(), $device->getUserIds());
        $this->assertNull($device->getHistoryLimits());
        $this->assertNull($device->getFeaturesCustomization());
        $this->assertSame($serial, $device->getSerial());
        $this->assertNotNull($device->getErrorTrackerDeviceId());
        $this->assertSame($owner->getId(), $device->getOwnerUserId());
    }

    public function testUpdate(): void
    {
        $device = Device::factory()->create();
        $product = Product::factory()->create();
        $hardware = Hardware::factory()->create();
        $firmware = Firmware::factory()->create();
        $users = User::factory(7)->create();
        $serial = Str::uuid()->__toString();
        $owner = User::factory()->create();
        $device = $this->getDeviceManager()->update($device, [
            'title' => 'newName',
            'serial' => $serial,
            'product' => $product,
            'firmware' => $firmware,
            'hardware' => $hardware,
            'owner' => $owner,
            'users' => $users->all(),
            'historyLimits' => ['config' => null],
        ], true);
        $this->assertSame('newName', $device->getTitle());
        $this->assertSame($product->getId(), $device->getProductId());
        $this->assertSame($hardware->getId(), $device->getHardwareId());
        $this->assertSame($firmware->getId(), $device->getFirmwareId());
        $this->assertEqualsCanonicalizing($users->pluck('id')->all(), $device->getUserIds());
        $this->assertSame($serial, $device->getSerial());
        $this->assertSame($owner->getId(), $device->getOwnerUserId());
        $this->assertNull($device->getHistoryLimits());
    }

    public function testDestroy(): void
    {
        $device = Device::factory()->create();
        $this->getDeviceManager()->destroy($device, true);
        $this->assertModelMissing($device);
    }
}
