<?php

namespace YektaSmart\IotServer\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface IFolderManager
{
    /**
     * @param array{owner?:int,title?:string,parent?:IFolder|int,containsDevice?:IDevice|int,containsFolder?:IFolder|int,userHasAccess?:int} $filters
     *
     * @return iterable<IFolder>
     */
    public function search(array $filters): iterable;

    /**
     * Only owner can create a sub-folder inside the folder.
     *
     * @param int[] $users additional users who access to this folder and it's devices. (Also access to it's sub-folders)
     */
    public function store(
        string $title,
        int|IFolder|null $parent,
        int|Authenticatable $owner,
        array $users = [],
        bool $userActivityLog = false
    ): IFolder;

    /**
     * Only owner can invoke this method.
     *
     * @param array{title?:string,parent?:IFolder|int,owner?:int|Authenticatable,users?:int[]} $changes
     */
    public function update(
        int|IFolder $folder,
        array $changes,
        bool $userActivityLog = false
    ): IFolder;

    /**
     * Only owner can destroy the folder.
     */
    public function destroy(int|IFolder $folder, bool $userActivityLog = false): void;
}
