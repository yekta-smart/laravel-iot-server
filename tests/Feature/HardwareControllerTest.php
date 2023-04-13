<?php

namespace YektaSmart\IotServer\Tests\Feature;

use dnj\AAA\Models\Type;
use dnj\AAA\Models\User;
use YektaSmart\IotServer\Contracts\IHardware;
use YektaSmart\IotServer\Models\Hardware;
use YektaSmart\IotServer\Tests\TestCase;

class HardwareControllerTest extends TestCase
{
    public function testSearchNotAuthenticated(): void
    {
        $this->getJson('iot-server/hardwares')->assertUnauthorized();
    }

    public function testSearchNotAuthorized(): void
    {
        $this->actingAs(User::factory()->create());
        $this->getJson('iot-server/hardwares')->assertForbidden();
    }

    public function testSearch(): void
    {
        $me = $this->createUserWithModelAbility(IHardware::class, 'viewAny');

        $myChildType = Type::factory()->create();
        $myChildType->parents()->attach($me->getTypeId());

        /**
         * @var User $myChild
         */
        $myChild = User::factory()->withType($myChildType)->create();

        // Unknown owner
        Hardware::factory()->create();

        /**
         * @var Hardware $myHardware Me as owner
         */
        $myHardware = Hardware::factory()->withOwner($me)->create();

        /**
         * @var Hardware $myChildHardware My child as owner
         */
        $myChildHardware = Hardware::factory()->withOwner($myChild)->create();

        $this->actingAs($me);
        $response = $this->getJson('iot-server/hardwares')->assertOk();
        $this->assertIsArray($response['data']);
        $this->assertCount(2, $response['data']);
        $this->assertEqualsCanonicalizing([$myHardware->id, $myChildHardware->id], array_column($response['data'], 'id'));

        $query = [
            'name' => $myHardware->name,
            'owner' => $myHardware->owner_id,
        ];
        $response = $this->getJson('iot-server/hardwares?'.http_build_query($query))
            ->assertOk()
            ->assertJson([
                'data' => [[
                    'id' => $myHardware->id,
                ]],
            ]);
    }

    public function testStoreNotAuthenticated(): void
    {
        $this->postJson('iot-server/hardwares')->assertUnauthorized();
    }

    public function testStoreNotAuthorized(): void
    {
        $this->actingAs(User::factory()->create());
        $this->postJson('iot-server/hardwares')->assertForbidden();
    }

    public function testStore(): void
    {
        $me = $this->createUserWithModelAbility(IHardware::class, 'create');

        $this->actingAs($me);
        $response = $this->postJson('iot-server/hardwares', [
            'name' => 'yekta-smart.hardware.sonoff',
            'version' => '1.0.0',
            'owner' => $me->getId(),
            'serial' => '1234567890123456789A123456789B12',
        ])->assertCreated();
        $this->assertIsArray($response['data']);
        $this->assertIsInt($response['data']['id']);
        $this->assertDatabaseHas(Hardware::class, ['id' => $response['data']['id']]);
    }

    public function testShowNotAuthenticated(): void
    {
        $this->getJson('iot-server/hardwares/'.Hardware::factory()->create()->getId())->assertUnauthorized();
    }

    public function testShowNotAuthorized(): void
    {
        $this->actingAs(User::factory()->create());
        $this->getJson('iot-server/hardwares/'.Hardware::factory()->create()->getId())->assertForbidden();
    }

    public function testShow(): void
    {
        $me = $this->createUserWithModelAbility(IHardware::class, 'view');

        /**
         * @var Hardware $myHardware Me as owner
         */
        $myHardware = Hardware::factory()->withOwner($me)->create();

        $this->actingAs($me);
        $this->getJson('iot-server/hardwares/'.$myHardware->getId())
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $myHardware->id,
                ],
            ]);
    }

    public function testUpdateNotAuthenticated(): void
    {
        $this->putJson('iot-server/hardwares/'.Hardware::factory()->create()->getId())->assertUnauthorized();
    }

    public function testUpdateNotAuthorized(): void
    {
        $this->actingAs(User::factory()->create());
        $this->putJson('iot-server/hardwares/'.Hardware::factory()->create()->getId())->assertForbidden();
    }

    public function testUpdate(): void
    {
        $me = $this->createUserWithModelAbility(IHardware::class, 'update');
        $hardware = Hardware::factory()->withOwner($me)->create();

        $this->actingAs($me);
        $response = $this->putJson('iot-server/hardwares/'.$hardware->getId(), [
            'name' => 'yekta-smart.hardware.sonoff2relay',
            'version' => '1.1.0',
            'serial' => '1234567890123456789A123456789B13',
        ])->assertOk();
        $this->assertIsArray($response['data']);
        $this->assertIsInt($response['data']['id']);
        $this->assertDatabaseHas(Hardware::class, ['id' => $response['data']['id']]);
    }

    public function testDestroyNotAuthenticated(): void
    {
        $this->deleteJson('iot-server/hardwares/'.Hardware::factory()->create()->getId())->assertUnauthorized();
    }

    public function testDestroyNotAuthorized(): void
    {
        $this->actingAs(User::factory()->create());
        $this->deleteJson('iot-server/hardwares/'.Hardware::factory()->create()->getId())->assertForbidden();
    }

    public function testDestroy(): void
    {
        $me = $this->createUserWithModelAbility(IHardware::class, 'delete');

        /**
         * @var Hardware $myHardware
         */
        $myHardware = Hardware::factory()->withOwner($me)->create();

        $this->actingAs($me);
        $this->deleteJson('iot-server/hardwares/'.$myHardware->getId())->assertNoContent();
        $this->assertModelMissing($myHardware);
    }
}
