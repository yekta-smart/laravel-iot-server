<?php

namespace YektaSmart\IotServer;

use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use YektaSmart\IotServer\Contracts\IFrameware;
use YektaSmart\IotServer\Contracts\IFramewareFeature;
use YektaSmart\IotServer\Contracts\IFramewareFeatureManager;
use YektaSmart\IotServer\Models\Frameware;
use YektaSmart\IotServer\Models\FramewareFeature;

class FramewareFeatureManager implements IFramewareFeatureManager
{
    public function __construct(protected ILogger $userLogger)
    {
    }

    /**
     * @return Collection<FramewareFeature>
     */
    public function search(array $filters): Collection
    {
        return FramewareFeature::query()->filter($filters)->get();
    }

    /**
     * @return Collection<FramewareFeature>
     */
    public function getByFrameware(int|IFrameware $frameware): Collection
    {
        return FramewareFeature::query()->where('frameware_id', Frameware::ensureId($frameware))->get();
    }

    public function findByCode(int|IFrameware $frameware, int $code): FramewareFeature
    {
        return FramewareFeature::query()
            ->where('frameware_id', Frameware::ensureId($frameware))
            ->where('code', $code)
            ->firstOrFail();
    }

    public function findById(int $id): FramewareFeature
    {
        return FramewareFeature::query()->findOrFail($id);
    }

    public function store(int|IFrameware $frameware, string $name, bool $userActivityLog = false): FramewareFeature
    {
        return DB::transaction(function () use ($frameware, $name, $userActivityLog) {
            /**
             * @var Frameware
             */
            $frameware = Frameware::query()->findOrFail(Frameware::ensureId($frameware));

            $code = FramewareFeature::assignCode($frameware, $name);

            /**
             * @var FramewareFeature
             */
            $feature = FramewareFeature::query()->create([
                'frameware_id' => $frameware,
                'name' => $name,
                'code' => $code,
            ]);

            if ($userActivityLog) {
                $this->userLogger->on($feature)
                    ->withRequest(request())
                    ->withProperties($feature->toArray())
                    ->log('created');
            }

            return $feature;
        });
    }

    public function trash(int|IFramewareFeature $feature, bool $userActivityLog = false): FramewareFeature
    {
        return DB::transaction(function () use ($feature, $userActivityLog) {
            /**
             * @var FramewareFeature
             */
            $feature = FramewareFeature::query()
                ->lockForUpdate()
                ->findOrFail(FramewareFeature::ensureId($feature));
            if ($feature->trashed()) {
                throw new \Exception('already trashed');
            }
            $feature->delete();

            if ($userActivityLog) {
                $this->userLogger->on($feature)
                    ->withRequest(request())
                    ->log('trashed');
            }

            return $feature;
        });
    }

    public function restore(int|IFramewareFeature $feature, bool $userActivityLog = false): FramewareFeature
    {
        return DB::transaction(function () use ($feature, $userActivityLog) {
            /**
             * @var FramewareFeature
             */
            $feature = FramewareFeature::query()
                ->lockForUpdate()
                ->findOrFail(FramewareFeature::ensureId($feature));
            if (!$feature->trashed()) {
                throw new \Exception('not trashed');
            }
            $feature->restore();

            if ($userActivityLog) {
                $this->userLogger->on($feature)
                    ->withRequest(request())
                    ->log('trashed');
            }

            return $feature;
        });
    }

    public function destroy(int|IFramewareFeature $feature, bool $userActivityLog = false): void
    {
        DB::transaction(function ($feature, $userActivityLog) {
            /**
             * @var FramewareFeature
             */
            $feature = FramewareFeature::query()
                ->lockForUpdate()
                ->findOrFail(FramewareFeature::ensureId($feature));
            $feature->delete();
            if ($userActivityLog) {
                $this->userLogger->on($feature)
                    ->withRequest(request())
                    ->withProperties($feature->toArray())
                    ->log('destroyed');
            }
        });
    }
}
