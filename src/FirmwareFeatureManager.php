<?php

namespace YektaSmart\IotServer;

use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use YektaSmart\IotServer\Contracts\IFirmware;
use YektaSmart\IotServer\Contracts\IFirmwareFeature;
use YektaSmart\IotServer\Contracts\IFirmwareFeatureManager;
use YektaSmart\IotServer\Models\Firmware;
use YektaSmart\IotServer\Models\FirmwareFeature;

class FirmwareFeatureManager implements IFirmwareFeatureManager
{
    public function __construct(protected ILogger $userLogger)
    {
    }

    /**
     * @return Collection<FirmwareFeature>
     */
    public function search(array $filters): Collection
    {
        return FirmwareFeature::query()->filter($filters)->get();
    }

    /**
     * @return Collection<FirmwareFeature>
     */
    public function getByFirmware(int|IFirmware $firmware): Collection
    {
        return FirmwareFeature::query()->where('firmware_id', Firmware::ensureId($firmware))->get();
    }

    public function findByCode(int|IFirmware $firmware, int $code): FirmwareFeature
    {
        return FirmwareFeature::query()
            ->where('firmware_id', Firmware::ensureId($firmware))
            ->where('code', $code)
            ->firstOrFail();
    }

    public function findById(int $id): FirmwareFeature
    {
        return FirmwareFeature::query()->findOrFail($id);
    }

    public function store(int|IFirmware $firmware, string $name, bool $userActivityLog = false): FirmwareFeature
    {
        return DB::transaction(function () use ($firmware, $name, $userActivityLog) {
            /**
             * @var Firmware
             */
            $firmware = Firmware::query()->findOrFail(Firmware::ensureId($firmware));

            $code = FirmwareFeature::assignCode($firmware, $name);

            /**
             * @var FirmwareFeature
             */
            $feature = FirmwareFeature::query()->create([
                'firmware_id' => $firmware->getId(),
                'name' => $name,
                'code' => $code,
            ]);

            if ($userActivityLog) {
                $this->userLogger->on($feature)
                    ->withRequest(app()->has('request') ? request() : null)
                    ->withProperties($feature->toArray())
                    ->log('created');
            }

            return $feature;
        });
    }

    public function trash(int|IFirmwareFeature $feature, bool $userActivityLog = false): FirmwareFeature
    {
        return DB::transaction(function () use ($feature, $userActivityLog) {
            /**
             * @var FirmwareFeature
             */
            $feature = FirmwareFeature::query()
                ->lockForUpdate()
                ->withTrashed()
                ->findOrFail(FirmwareFeature::ensureId($feature));
            if ($feature->trashed()) {
                throw new \Exception('already trashed');
            }
            $feature->delete();

            if ($userActivityLog) {
                $this->userLogger->on($feature)
                    ->withRequest(app()->has('request') ? request() : null)
                    ->log('trashed');
            }

            return $feature;
        });
    }

    public function restore(int|IFirmwareFeature $feature, bool $userActivityLog = false): FirmwareFeature
    {
        return DB::transaction(function () use ($feature, $userActivityLog) {
            /**
             * @var FirmwareFeature
             */
            $feature = FirmwareFeature::query()
                ->lockForUpdate()
                ->withTrashed()
                ->findOrFail(FirmwareFeature::ensureId($feature));
            if (!$feature->trashed()) {
                throw new \Exception('not trashed');
            }
            $feature->restore();

            if ($userActivityLog) {
                $this->userLogger->on($feature)
                    ->withRequest(app()->has('request') ? request() : null)
                    ->log('trashed');
            }

            return $feature;
        });
    }

    public function destroy(int|IFirmwareFeature $feature, bool $userActivityLog = false): void
    {
        DB::transaction(function () use ($feature, $userActivityLog) {
            /**
             * @var FirmwareFeature
             */
            $feature = FirmwareFeature::query()
                ->lockForUpdate()
                ->withTrashed()
                ->findOrFail(FirmwareFeature::ensureId($feature));
            $feature->forceDelete();
            if ($userActivityLog) {
                $this->userLogger->on($feature)
                    ->withRequest(app()->has('request') ? request() : null)
                    ->withProperties($feature->toArray())
                    ->log('destroyed');
            }
        });
    }
}
