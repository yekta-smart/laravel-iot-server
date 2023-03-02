<?php

namespace YektaSmart\IotServer;

use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use YektaSmart\IotServer\Contracts\IFolder;
use YektaSmart\IotServer\Contracts\IFolderManager;
use YektaSmart\IotServer\Models\Folder;

class FolderManager implements IFolderManager
{
    public function __construct(protected ILogger $userLogger)
    {
    }

    /**
     * @return Collection<Folder>
     */
    public function search(array $filters): Collection
    {
        return Folder::query()->filter($filters)->get();
    }

    public function store(
        string $title,
        int|IFolder|null $parent,
        int|Authenticatable $owner,
        array $users = [],
        bool $userActivityLog = false
    ): Folder {
        return DB::transaction(function () use ($title, $parent, $owner, $users, $userActivityLog) {
            $owner = UserUtil::ensureId($owner);
            $users = array_map([UserUtil::class, 'ensureId'], $users);

            if ($parent) {
                if (is_int($parent)) {
                    /**
                     * @var Folder
                     */
                    $parent = Folder::query()->findOrFail(Folder::ensureId($parent));
                }
                if ($parent->getOwnerUserId() != $owner) {
                    throw new \Exception("Owner doesn't match with parent");
                }
            }

            /**
             * @var Folder
             */
            $folder = Folder::query()->create([
                'title' => $title,
                'parent_id' => $parent,
                'owner_id' => $owner,
            ]);
            $folder->users()->sync($users);

            if ($userActivityLog) {
                $this->userLogger->on($folder)
                    ->withRequest(request())
                    ->withProperties($folder->toArray())
                    ->log('created');
            }

            return $folder;
        });
    }

    /**
     * Only owner can invoke this method.
     *
     * @param array{title?:string,parent?:IFolder|int,owner?:int|Authenticatable,users?:int[]} $changes
     */
    public function update(
        int|IFolder $folder,
        array $changes,
        bool $userActivityLog = false
    ): Folder {
        return DB::transaction(function () use ($folder, $changes, $userActivityLog) {
            /**
             * @var Folder
             */
            $folder = Folder::query()
                ->lockForUpdate()
                ->findOrFail(Folder::ensureId($folder));

            if (isset($changes['owner'])) {
                if (!$folder->canChangeOwnerTo($changes['owner'])) {
                    throw new \Exception();
                }

                $changes['owner_id'] = UserUtil::ensureId($changes['owner']);
                unset($changes['owner']);
            }
            if (isset($changes['parent'])) {
                $changes['parent_id'] = Folder::ensureId($changes['parent']);
                unset($changes['parent']);

                if (!$folder->canChangeParentTo($changes['parent_id'])) {
                    throw new \Exception();
                }
            }

            if (isset($changes['users'])) {
                $users = array_map([UserUtil::class, 'ensureId'], $changes['users']);
                $folder->users()->sync($users);
                unset($changes['users']);
            }

            $folder->fill($changes);
            $changes = $folder->changesForLog();
            $folder->save();
            if ($userActivityLog) {
                $this->userLogger->on($folder)
                    ->withRequest(request())
                    ->withProperties($changes)
                    ->log('updated');
            }
        });
    }

    public function destroy(int|IFolder $folder, bool $userActivityLog = false): void
    {
        DB::transaction(function ($folder, $userActivityLog) {
            /**
             * @var Folder
             */
            $folder = Folder::query()
                ->lockForUpdate()
                ->findOrFail(Folder::ensureId($folder));
            $folder->delete();
            if ($userActivityLog) {
                $this->userLogger->on($folder)
                    ->withRequest(request())
                    ->withProperties($folder->toArray())
                    ->log('destroyed');
            }
        });
    }
}
