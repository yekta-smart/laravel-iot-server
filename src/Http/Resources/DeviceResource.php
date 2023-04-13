<?php

namespace YektaSmart\IotServer\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use YektaSmart\IotServer\Contracts\IDevice;
use YektaSmart\IotServer\Contracts\IDeviceConfig;
use YektaSmart\IotServer\Contracts\IDeviceConfigManager;
use YektaSmart\IotServer\Contracts\IDeviceState;
use YektaSmart\IotServer\Contracts\IDeviceStateManager;
use YektaSmart\IotServer\Contracts\IFirmwareManager;
use YektaSmart\IotServer\Contracts\IHardwareManager;
use YektaSmart\IotServer\Contracts\IPeerRegistery;
use YektaSmart\IotServer\Contracts\IProductManager;

/**
 * @property IDevice $resource
 */
class DeviceResource extends JsonResource
{
    protected bool $withState = false;
    protected bool $withConfig = false;

    public function toArray($request)
    {
        $result = parent::toArray($request);

        /**
         * @var IDeviceConfig|null
         */
        $config = app(IDeviceConfigManager::class)->getLatest($this->resource->getId());

        /**
         * @var IDeviceState|null
         */
        $state = app(IDeviceStateManager::class)->getLatest($this->resource->getId());

        /**
         * @var IPeerRegistery
         */
        $peerRegistery = app(IPeerRegistery::class);

        $result['product'] = ProductResource::make(app(IProductManager::class)->find($this->resource->getProductId()));
        $result['hardware'] = HardwareResource::make(app(IHardwareManager::class)->find($this->resource->getHardwareId()));
        $result['online'] = $peerRegistery->hasDevice($this->resource);
        $result['firmware'] = app(IFirmwareManager::class)->find($this->resource->getFirmwareId());
        if ($this->withState) {
            $result['state'] = $state;
        }
        if ($this->withConfig) {
            $result['config'] = $config;
        }

        return $result;
    }

    public function withState(bool $withState = true): static
    {
        $this->withState = $withState;

        return $this;
    }

    public function withConfig(bool $withConfig = true): static
    {
        $this->withConfig = $withConfig;

        return $this;
    }
}
