<?php

namespace YektaSmart\IotServer\Contracts;

interface IFramewareFeatureManager
{
    /**
     * @param array{frameware?:int|IFrameware,name?:string} $filters
     *
     * @return iterable<IFramewareFeature>
     */
    public function search(array $filters = []): iterable;

    public function store(int|IFrameware $frameware, string $name, bool $userActivityLog = false): IFramewareFeature;

    public function softDelete(int|IFramewareFeature $feature, bool $userActivityLog = false): IFramewareFeature;

    public function destroy(int|IFramewareFeature $feature, bool $userActivityLog = false): void;

    /**
     * @return iterable<IFramewareFeature>
     */
    public function getByFrameware(int|IFrameware $frameware): iterable;

    public function findByCode(int|IFrameware $frameware, int $code): IFramewareFeature;

    public function findById(int $id): IFramewareFeature;
}
