<?php

namespace YektaSmart\IotServer;

use dnj\AAA\Models\User;
use dnj\Filesystem\Contracts\IFile;
use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use YektaSmart\IotServer\Contracts\IFirmware;
use YektaSmart\IotServer\Contracts\IFirmwareManager;
use YektaSmart\IotServer\Models\Firmware;
use YektaSmart\IotServer\Models\FirmwareFeature;
use YektaSmart\IotServer\Models\Hardware;

class FirmwareManager implements IFirmwareManager
{
    public function __construct(protected ILogger $userLogger)
    {
    }

    public function find(int $id): ?Firmware
    {
        return Firmware::query()->find($id);
    }

    public function findOrFail(int $id): Firmware
    {
        return Firmware::query()->findOrFail($id);
    }

    public function findBySerial(string $serial): ?Firmware
    {
        return Firmware::query()->where('serial', $serial)->first();
    }

    public function findBySerialOrFail(string $serial): Firmware
    {
        return Firmware::query()->where('serial', $serial)->firstOrFail();
    }

    /**
     * @return Collection<Firmware>
     */
    public function search(array $filters): Collection
    {
        return Firmware::query()->filter($filters)->get();
    }

    /**
     * @param string[]             $features
     * @param array<IHardware|int> $hardwares
     */
    public function store(
        string $name,
        string $version,
        IFile $file,
        array $features,
        array $hardwares,
        int|Authenticatable $owner,
        ?string $serial = null,
        bool $userActivityLog = false
    ): Firmware {
        return DB::transaction(function () use ($name, $version, $file, $features, $hardwares, $owner, $serial, $userActivityLog) {
            $hardwares = array_map([Hardware::class, 'ensureId'], $hardwares);
            $owner = User::ensureId($owner);

            /**
             * @var Firmware|null
             */
            $first = Firmware::query()->where('name', $name)->first();
            if ($first and $first->owner_id != $owner) {
                throw new \Exception('owner does not match');
            }

            /**
             * @var Firmware
             */
            $firmware = Firmware::query()->create([
                'name' => $name,
                'version' => $version,
                'file' => $file,
                'owner_id' => $owner,
                'serial' => $serial ?? str_replace('-', '', Str::uuid()),
            ]);
            $firmware->hardwares()->sync($hardwares);
            $firmware->features()->createMany(array_map(fn ($name) => [
                'name' => $name,
                'code' => FirmwareFeature::assignCode($firmware, $name),
            ], $features));

            if ($userActivityLog) {
                $this->userLogger->on($firmware)
                    ->withRequest(request())
                    ->withProperties($firmware->toArray())
                    ->log('created');
            }

            return $firmware;
        });
    }

    public function update(int|IFirmware $firmware, array $changes, bool $userActivityLog = false): Firmware
    {
        return DB::transaction(function () use ($firmware, $changes, $userActivityLog) {
            /**
             * @var Firmware
             */
            $firmware = Firmware::query()
                ->lockForUpdate()
                ->findOrFail(Firmware::ensureId($firmware));

            if (isset($changes['hardwares'])) {
                $hardwares = array_map([Hardware::class, 'ensureId'], $changes['hardwares']);
                $firmware->hardwares()->sync($hardwares);
                unset($changes['hardwares']);
            }
            if (isset($changes['owner'])) {
                $changes['owner_id'] = User::ensureId($changes['owner']);
                unset($changes['owner']);
            }
            $firmware->fill($changes);
            $changes = $firmware->changesForLog();
            $firmware->save();
            if ($userActivityLog) {
                $this->userLogger->on($firmware)
                    ->withRequest(request())
                    ->withProperties($changes)
                    ->log('updated');
            }

            return $firmware;
        });
    }

    public function destroy(int|IFirmware $firmware, bool $userActivityLog = false): void
    {
        DB::transaction(function () use ($firmware, $userActivityLog) {
            /**
             * @var Firmware
             */
            $firmware = Firmware::query()
                ->lockForUpdate()
                ->findOrFail(Firmware::ensureId($firmware));
            $firmware->delete();
            if ($userActivityLog) {
                $this->userLogger->on($firmware)
                    ->withRequest(request())
                    ->withProperties($firmware->toArray())
                    ->log('destroyed');
            }
        });
    }
}
