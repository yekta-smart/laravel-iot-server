<?php

namespace YektaSmart\IotServer;

use dnj\Filesystem\Contracts\IFile;
use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use YektaSmart\IotServer\Contracts\IFrameware;
use YektaSmart\IotServer\Contracts\IFramewareManager;
use YektaSmart\IotServer\Models\Frameware;
use YektaSmart\IotServer\Models\FramewareFeature;

class FramewareManager implements IFramewareManager
{
    public function __construct(protected ILogger $userLogger)
    {
    }

    /**
     * @return Collection<Frameware>
     */
    public function search(array $filters): Collection
    {
        return Frameware::query()->filter($filters)->get();
    }

    /**
     * @param string[]             $features
     * @param array<IHardware|int> $hardwares
     */
    public function store(string $name, string $version, IFile $file, array $features, array $hardwares, int|Authenticatable $owner, bool $userActivityLog = false): Frameware
    {
        return DB::transaction(function () use ($name, $version, $file, $features, $hardwares, $owner, $userActivityLog) {
            $hardwares = array_map([Hardware::class, 'ensureId'], $hardwares);
            $owner = UserUtil::ensureId($owner);

            /**
             * @var Frameware|null
             */
            $first = Frameware::query()->where('name', $name)->first();
            if ($first and $first->owner_id != $owner) {
                throw new \Exception('owner does not match');
            }

            /**
             * @var Frameware
             */
            $frameware = Frameware::query()->create([
                'name' => $name,
                'version' => $version,
                'file' => $file,
                'owner' => $owner,
            ]);
            $frameware->hardwares()->sync($hardwares);
            $frameware->features()->createMany(array_map(fn ($name) => [
                'name' => $name,
                'code' => FramewareFeature::assignCode($frameware, $name),
            ], $features));

            if ($userActivityLog) {
                $this->userLogger->on($frameware)
                    ->withRequest(request())
                    ->withProperties($frameware->toArray())
                    ->log('created');
            }

            return $frameware;
        });
    }

    public function update(int|IFrameware $product, array $changes, bool $userActivityLog = false): Frameware
    {
        return DB::transaction(function () use ($changes, $userActivityLog) {
            /**
             * @var Frameware
             */
            $frameware = Frameware::query()
                ->lockForUpdate()
                ->findOrFail(Frameware::ensureId($frameware));

            if (isset($changes['hardwares'])) {
                $hardwares = array_map([Frameware::class, 'ensureId'], $changes['hardwares']);
                $frameware->hardwares()->sync($hardwares);
                unset($changes['hardwares']);
            }
            if (isset($changes['owner'])) {
                $changes['owner_id'] = UserUtil::ensureId($changes['owner']);
                unset($changes['owner']);
            }
            $frameware->fill($changes);
            $changes = $frameware->changesForLog();
            $frameware->save();
            if ($userActivityLog) {
                $this->userLogger->on($frameware)
                    ->withRequest(request())
                    ->withProperties($changes)
                    ->log('updated');
            }
        });
    }

    public function destroy(int|IFrameware $hardware, bool $userActivityLog = false): void
    {
        DB::transaction(function ($hardware, $userActivityLog) {
            /**
             * @var Frameware
             */
            $hardware = Frameware::query()
                ->lockForUpdate()
                ->findOrFail(Frameware::ensureId($hardware));
            $hardware->delete();
            if ($userActivityLog) {
                $this->userLogger->on($hardware)
                    ->withRequest(request())
                    ->withProperties($hardware->toArray())
                    ->log('destroyed');
            }
        });
    }
}
