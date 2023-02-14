<?php

namespace YektaSmart\IotServer\Contracts;

use dnj\AAA\Contracts\IOwnerableModel;

interface IProduct extends IOwnerableModel
{
    public function getId(): int;

    public function getTitle(): string;

    /**
     * @return class-string<IDeviceHandler>
     */
    public function getDeviceHandler(): string;

    /**
     * @param int[]
     */
    public function getHardwareIds(): array;

    /**
     * @param int[]
     */
    public function getFramewareIds(): array;

    /**
     * @return array{count:int|null,age:int|null}|null
     */
    public function getStateHistoryLimits(): ?array;

    /**
     * @return int[]|null
     */
    public function getDefaultFeatureIds(int|IFrameware $frameware): ?array;

    /**
     * @see https://github.com/dnj/php-error-tracker-contracts
     *
     * @return int id of IApp model
     */
    public function getErrorTrackerAppId(): int;
}
