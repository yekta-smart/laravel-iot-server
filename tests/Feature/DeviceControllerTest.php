<?php

namespace YektaSmart\IotServer\Tests\Feature;

use dnj\AAA\Models\Type;
use dnj\AAA\Models\User;
use YektaSmart\IotServer\Contracts\IDevice;
use YektaSmart\IotServer\Models\Device;
use YektaSmart\IotServer\Models\Firmware;
use YektaSmart\IotServer\Models\Hardware;
use YektaSmart\IotServer\Models\Product;
use YektaSmart\IotServer\Tests\TestCase;

class DeviceControllerTest extends TestCase
{
    public function testSearchNotAuthenticated(): void
    {
        $this->getJson('iot-server/devices')->assertUnauthorized();
    }

    public function testSearchNotAuthorized(): void
    {
        $this->actingAs(User::factory()->create());
        $this->getJson('iot-server/devices')->assertForbidden();
    }

    public function testSearch(): void
    {
        $me = $this->createUserWithModelAbility(IDevice::class, 'viewAny');

        $myChildType = Type::factory()->create();
        $myChildType->parents()->attach($me->getTypeId());

        /**
         * @var User $myChild
         */
        $myChild = User::factory()->withType($myChildType)->create();

        // Unknown owner
        Device::factory()->create();

        /**
         * @var Device $myDevice Me as owner
         */
        $myDevice = Device::factory()->withOwner($me)->create();

        /**
         * @var Device $myChildDevice My child as owner
         */
        $myChildDevice = Device::factory()->withOwner($myChild)->create();

        $this->actingAs($me);
        $response = $this->getJson('iot-server/devices')->assertOk();
        $this->assertIsArray($response['data']);
        $this->assertCount(2, $response['data']);
        $this->assertEqualsCanonicalizing([$myDevice->id, $myChildDevice->id], array_column($response['data'], 'id'));

        $query = [
            'title' => $myDevice->title,
            'product' => $myDevice->product_id,
            'hardware' => $myDevice->hardware_id,
            'firmware' => $myDevice->firmware_id,
            'owner' => $myDevice->owner_id,
        ];
        $response = $this->getJson('iot-server/devices?'.http_build_query($query))
            ->assertOk()
            ->assertJson([
                'data' => [[
                    'id' => $myDevice->id,
                ]],
            ]);
    }

    public function testStoreNotAuthenticated(): void
    {
        $this->postJson('iot-server/devices')->assertUnauthorized();
    }

    public function testStoreNotAuthorized(): void
    {
        $this->actingAs(User::factory()->create());
        $this->postJson('iot-server/devices')->assertForbidden();
    }

    public function testStore(): void
    {
        $me = $this->createUserWithModelAbility(IDevice::class, 'create');
        $product = Product::factory()->withOwner($me)->create();
        $hardware = Hardware::factory()->withOwner($me)->create();
        $firmware = Firmware::factory()->withOwner($me)->create();

        $this->actingAs($me);
        $response = $this->postJson('iot-server/devices', [
            'title' => 'my device',
            'product' => $product->getId(),
            'hardware' => $hardware->getId(),
            'firmware' => $firmware->getId(),
            'owner' => $me->getId(),
            'serial' => '1234567890123456789A123456789B12',
        ])->assertCreated();
        $this->assertIsArray($response['data']);
        $this->assertIsInt($response['data']['id']);
        $this->assertDatabaseHas(Device::class, ['id' => $response['data']['id']]);
    }

    public function testShowNotAuthenticated(): void
    {
        $this->getJson('iot-server/devices/'.Device::factory()->create()->getId())->assertUnauthorized();
    }

    public function testShowNotAuthorized(): void
    {
        $this->actingAs(User::factory()->create());
        $this->getJson('iot-server/devices/'.Device::factory()->create()->getId())->assertForbidden();
    }

    public function testShow(): void
    {
        $me = $this->createUserWithModelAbility(IDevice::class, 'view');

        /**
         * @var Device $myDevice Me as owner
         */
        $myDevice = Device::factory()->withOwner($me)->create();

        $this->actingAs($me);
        $this->getJson('iot-server/devices/'.$myDevice->getId())
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $myDevice->id,
                ],
            ]);
    }

    public function testUpdateNotAuthenticated(): void
    {
        $this->putJson('iot-server/devices/'.Device::factory()->create()->getId())->assertUnauthorized();
    }

    public function testUpdateNotAuthorized(): void
    {
        $this->actingAs(User::factory()->create());
        $this->putJson('iot-server/devices/'.Device::factory()->create()->getId())->assertForbidden();
    }

    public function testUpdate(): void
    {
        $me = $this->createUserWithModelAbility(IDevice::class, 'update');
        $device = Device::factory()->withOwner($me)->create();
        $product = Product::factory()->withOwner($me)->create();
        $hardware = Hardware::factory()->withOwner($me)->create();
        $firmware = Firmware::factory()->withOwner($me)->create();

        $this->actingAs($me);
        $response = $this->putJson('iot-server/devices/'.$device->getId(), [
            'title' => 'my device',
            'product' => $product->getId(),
            'hardware' => $hardware->getId(),
            'firmware' => $firmware->getId(),
            'owner' => $me->getId(),
            'serial' => '1234567890123456789A123456789B12',
        ])->assertOk();
        $this->assertIsArray($response['data']);
        $this->assertIsInt($response['data']['id']);
        $this->assertDatabaseHas(Device::class, ['id' => $response['data']['id']]);
    }

    public function testDestroyNotAuthenticated(): void
    {
        $this->deleteJson('iot-server/devices/'.Device::factory()->create()->getId())->assertUnauthorized();
    }

    public function testDestroyNotAuthorized(): void
    {
        $this->actingAs(User::factory()->create());
        $this->deleteJson('iot-server/devices/'.Device::factory()->create()->getId())->assertForbidden();
    }

    public function testDestroy(): void
    {
        $me = $this->createUserWithModelAbility(IDevice::class, 'delete');

        /**
         * @var Device $myDevice
         */
        $myDevice = Device::factory()->withOwner($me)->create();

        $this->actingAs($me);
        $this->deleteJson('iot-server/devices/'.$myDevice->getId())->assertNoContent();
        $this->assertModelMissing($myDevice);
    }
}
