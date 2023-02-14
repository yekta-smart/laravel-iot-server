<?php

namespace YektaSmart\IotServer\Contracts;

use dnj\Filesystem\Contracts\IFile;

interface IFramewareManager
{
    /**
     * @param array{owner?:int|null,title?:string,compatibleWithHardware?:int} $filters
     *
     * @return iterable<IFrameware>
     */
    public function search(array $filters = []): iterable;

    /**
     * @param string[]             $features
     * @param array<IHardware|int> $compatibleHardwares
     */
    public function store(string $title, string $semVer, IFile $file, array $features, array $compatibleHardwares, ?int $owner, bool $userActivityLog = false): IFrameware;

    /**
     * @param array{title?:string,semVer?:string,file?:IFile,features?:array<IFramewareFeature|int>,compatibleHardwares?:array<IHardware|int>,owner?:int|null}
     */
    public function update(int|IFrameware $frameware, array $changes, bool $userActivityLog = false): IFrameware;

    public function destroy(int|IFrameware $frameware, bool $userActivityLog = false): void;
}
