<?php

namespace YektaSmart\IotServer\Tests\Feature;

use dnj\AAA\Models\Type;
use dnj\AAA\Models\User;
use YektaSmart\IotServer\Contracts\IProduct;
use YektaSmart\IotServer\Models\Product;
use YektaSmart\IotServer\Tests\DummyDeviceHandler;
use YektaSmart\IotServer\Tests\TestCase;

class ProductControllerTest extends TestCase
{
    public function testSearchNotAuthenticated(): void
    {
        $this->getJson('iot-server/products')->assertUnauthorized();
    }

    public function testSearchNotAuthorized(): void
    {
        $this->actingAs(User::factory()->create());
        $this->getJson('iot-server/products')->assertForbidden();
    }

    public function testSearch(): void
    {
        $me = $this->createUserWithModelAbility(IProduct::class, 'viewAny');

        $myChildType = Type::factory()->create();
        $myChildType->parents()->attach($me->getTypeId());

        /**
         * @var User $myChild
         */
        $myChild = User::factory()->withType($myChildType)->create();

        // Unknown owner
        Product::factory()->create();

        /**
         * @var Product $myProduct Me as owner
         */
        $myProduct = Product::factory()->withOwner($me)->create();

        /**
         * @var Product $myChildProduct My child as owner
         */
        $myChildProduct = Product::factory()->withOwner($myChild)->create();

        $this->actingAs($me);
        $response = $this->getJson('iot-server/products')->assertOk();
        $this->assertIsArray($response['data']);
        $this->assertCount(2, $response['data']);
        $this->assertEqualsCanonicalizing([$myProduct->id, $myChildProduct->id], array_column($response['data'], 'id'));

        $query = [
            'name' => $myProduct->name,
            'owner' => $myProduct->owner_id,
        ];
        $response = $this->getJson('iot-server/products?'.http_build_query($query))
            ->assertOk()
            ->assertJson([
                'data' => [[
                    'id' => $myProduct->id,
                ]],
            ]);
    }

    public function testStoreNotAuthenticated(): void
    {
        $this->postJson('iot-server/products')->assertUnauthorized();
    }

    public function testStoreNotAuthorized(): void
    {
        $this->actingAs(User::factory()->create());
        $this->postJson('iot-server/products')->assertForbidden();
    }

    public function testStore(): void
    {
        $me = $this->createUserWithModelAbility(IProduct::class, 'create');
        $this->actingAs($me);
        $serial = '1234567890123456789A123456789B12';
        $response = $this->postJson('iot-server/products', [
            'title' => 'Remote On/Off Switch',
            'deviceHandler' => DummyDeviceHandler::class,
            'owner' => $me->getId(),
            'serial' => $serial,
        ])->assertCreated();
        $this->assertIsArray($response['data']);
        $this->assertIsInt($response['data']['id']);
        $this->assertDatabaseHas(Product::class, [
            'id' => $response['data']['id'],
            'serial' => $serial,
        ]);
    }

    public function testShowNotAuthenticated(): void
    {
        $this->getJson('iot-server/products/'.Product::factory()->create()->getId())->assertUnauthorized();
    }

    public function testShowNotAuthorized(): void
    {
        $this->actingAs(User::factory()->create());
        $this->getJson('iot-server/products/'.Product::factory()->create()->getId())->assertForbidden();
    }

    public function testShow(): void
    {
        $me = $this->createUserWithModelAbility(IProduct::class, 'view');

        /**
         * @var Product $myProduct Me as owner
         */
        $myProduct = Product::factory()->withOwner($me)->create();

        $this->actingAs($me);
        $this->getJson('iot-server/products/'.$myProduct->getId())
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $myProduct->id,
                ],
            ]);
    }

    public function testUpdateNotAuthenticated(): void
    {
        $this->putJson('iot-server/products/'.Product::factory()->create()->getId())->assertUnauthorized();
    }

    public function testUpdateNotAuthorized(): void
    {
        $this->actingAs(User::factory()->create());
        $this->putJson('iot-server/products/'.Product::factory()->create()->getId())->assertForbidden();
    }

    public function testUpdate(): void
    {
        $me = $this->createUserWithModelAbility(IProduct::class, 'update');
        $product = Product::factory()->withOwner($me)->create();

        $this->actingAs($me);
        $serial = '1234567890123456789A123456789B13';
        $response = $this->putJson('iot-server/products/'.$product->getId(), [
            'title' => 'Remote OnOff Switch',
            'serial' => $serial,
        ])->assertOk();
        $this->assertIsArray($response['data']);
        $this->assertIsInt($response['data']['id']);
        $this->assertDatabaseHas(Product::class, [
            'id' => $response['data']['id'],
            'serial' => $serial,
        ]);
    }

    public function testDestroyNotAuthenticated(): void
    {
        $this->deleteJson('iot-server/products/'.Product::factory()->create()->getId())->assertUnauthorized();
    }

    public function testDestroyNotAuthorized(): void
    {
        $this->actingAs(User::factory()->create());
        $this->deleteJson('iot-server/products/'.Product::factory()->create()->getId())->assertForbidden();
    }

    public function testDestroy(): void
    {
        $me = $this->createUserWithModelAbility(IProduct::class, 'delete');

        /**
         * @var Product $myProduct
         */
        $myProduct = Product::factory()->withOwner($me)->create();

        $this->actingAs($me);
        $this->deleteJson('iot-server/products/'.$myProduct->getId())->assertNoContent();
        $this->assertModelMissing($myProduct);
    }
}
