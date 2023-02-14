<?php

namespace YektaSmart\IotServer\Contracts;

use dnj\Filesystem\Contracts\IDirectory;

interface IFolderManager
{
    /**
     * @param array{owner?:int,title?:string,parent?:IDirectory|int,containsDevice?:IDevice|int,containsFolder?:IFolder|int,userHasAccess?:int} $filters
     *
     * @return iterable<IFolder>
     */
    public function search(array $filters = []): iterable;

    /**
     * Only owner can create a sub-folder inside the folder.
     *
     * @param int|null $owner if owner set null, current authenticated user will use
     * @param int[]    $users additional users who access to this folder and it's devices. (Also access to it's sub-folders)
     */
    public function store(
        string $title,
        int|IDirectory|null $parent,
        ?int $owner = null,
        array $users = [],
        bool $userActivity = false
    ): IFolder;

    /**
     * Only owner can invoke this method.
     *
     * @param array{title?:string,parent?:IDirectory|int,owner?:int,users?:int[]} $changes
     */
    public function update(
        int|IFolder $folder,
        array $changes,
        bool $userActivity = false
    ): IFolder;

    /**
     * Only owner can destroy the folder.
     */
    public function destroy(int|IFolder $folder, bool $userActivityLog = false): void;
}
