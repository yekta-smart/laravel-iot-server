<?php

namespace YektaSmart\IotServer\Contracts;

use dnj\Filesystem\Contracts\IFile;
use Illuminate\Contracts\Auth\Authenticatable;

interface IFramewareManager
{
    /**
     * @param array{owner?:int|null,name?:string,compatibleWithHardware?:int} $filters
     *
     * @return iterable<IFrameware>
     */
    public function search(array $filters): iterable;

    /**
     * @param string[]             $features
     * @param array<IHardware|int> $hardwares
     */
    public function store(string $name, string $version, IFile $file, array $features, array $hardwares, int|Authenticatable $owner, bool $userActivityLog = false): IFrameware;

    /**
     * @param array{name?:string,version?:string,file?:IFile,hardwares?:array<IHardware|int>,owner?:int|Authenticatable} $changes
     */
    public function update(int|IFrameware $frameware, array $changes, bool $userActivityLog = false): IFrameware;

    public function destroy(int|IFrameware $frameware, bool $userActivityLog = false): void;
}
