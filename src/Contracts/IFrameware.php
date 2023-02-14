<?php

namespace YektaSmart\IotServer\Contracts;

use dnj\AAA\Contracts\IOwnerableModel;
use dnj\Filesystem\Contracts\IFile;

interface IFrameware extends IOwnerableModel, IHasSemVer
{
    public function getId(): int;

    public function getName(): string;

    public function getFile(): IFile;

    /**
     * @return int[]
     */
    public function getProductIds(): array;

    /**
     * @return int[]
     */
    public function getHardwareIds(): array;
}
