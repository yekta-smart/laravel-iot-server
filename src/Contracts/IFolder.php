<?php

namespace YektaSmart\IotServer\Contracts;

use dnj\AAA\Contracts\IOwnerableModel;

interface IFolder extends IOwnerableModel
{
    public function getId(): int;

    public function getParentId(): ?int;

    public function getOwnerUserId(): int;

    /**
     * Additional user ids which access to this folder and it's devices.
     *
     * @return int[]
     */
    public function getUserIds(): array;

    public function getTitle(): string;

    /**
     * @return int[]
     */
    public function getDeviceIds(): array;

    /**
     * Getter for sub-folders.
     *
     * @return int[]
     */
    public function getFolderIds(): array;
}
