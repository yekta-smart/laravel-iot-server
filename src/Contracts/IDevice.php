<?php

namespace YektaSmart\IotServer\Contracts;

use dnj\AAA\Contracts\IOwnerableModel;

interface IDevice extends IOwnerableModel
{
    public function getId(): string;

    public function getTitle(): string;

    /**
     * Additional user ids which access to this device.
     *
     * @return int[]
     */
    public function getUserIds(): array;

    /**
     * @return array{config:array{count:int|null,age:int|null},state:array{count:int|null,age:int|null}}|null
     */
    public function getHistoryLimits(): ?array;

    public function getProductId(): int;

    public function getHardwareId(): int;

    public function getFramewareId(): int;

    /**
     * @return array{enabledIds:int[],disabledIds:int[]}|null
     */
    public function getFeaturesCustomization(): ?array;

    /**
     * @see https://github.com/dnj/php-error-tracker-contracts
     *
     * @return int id of IDevice model
     */
    public function getErrorTrackerDeviceId(): int;

    /**
     * @return IDeviceConfig|null latest config, if there was any
     */
    public function getConfig(): ?IDeviceConfig;

    /**
     * @return IDeviceState|null latest state, if there was any
     */
    public function getState(): ?IDeviceState;

    public function getHandler(): IDeviceHandler;
}
