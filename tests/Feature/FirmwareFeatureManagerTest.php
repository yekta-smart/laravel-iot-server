<?php

namespace YektaSmart\IotServer\Tests\Feature;

use YektaSmart\IotServer\Models\Firmware;
use YektaSmart\IotServer\Models\FirmwareFeature;
use YektaSmart\IotServer\Tests\TestCase;

class FirmwareFeatureManagerTest extends TestCase
{
    public function testFind(): void
    {
        $firmware = Firmware::factory()->create();
        $this->assertEmpty($this->getFirmwareFeatureManager()->getByFirmware($firmware));
        $features = FirmwareFeature::factory(5)->withFirmware($firmware)->create();
        $this->assertCount(5, $this->getFirmwareFeatureManager()->getByFirmware($firmware));
        $this->assertSame($features[0]->getId(), $this->getFirmwareFeatureManager()->findByCode($firmware, $features[0]->getCode())->getId());
        $this->assertSame($features[1]->getId(), $this->getFirmwareFeatureManager()->findById($features[1]->getId())->getId());
    }

    public function testStore(): void
    {
        $firmware = Firmware::factory()->create();
        $feature = $this->getFirmwareFeatureManager()->store($firmware, 'feature_1', true);
        $this->assertSame($firmware->getId(), $feature->getFirmwareId());
        $this->assertSame('feature_1', $feature->getName());
    }

    public function testSoftDelete(): void
    {
        $feature = FirmwareFeature::factory()->create();
        $feature = $this->getFirmwareFeatureManager()->trash($feature, true);
        $this->assertTrue($feature->trashed());
        $feature = $this->getFirmwareFeatureManager()->restore($feature, true);
        $this->assertFalse($feature->trashed());
    }

    public function testDestroy(): void
    {
        $feature = FirmwareFeature::factory()->create();
        $this->getFirmwareFeatureManager()->destroy($feature, true);
        $this->assertModelMissing($feature);
    }
}
