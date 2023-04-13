<?php

namespace YektaSmart\IotServer\Models;

use Carbon\Carbon;
use dnj\UserLogger\Concerns\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use YektaSmart\IotServer\Contracts\IFirmware;
use YektaSmart\IotServer\Contracts\IFirmwareFeature;
use YektaSmart\IotServer\Database\Factories\FirmwareFeatureFactory;

/**
 * @property int         $id
 * @property int         $firmware_id
 * @property Firmware    $firmware
 * @property string      $name
 * @property int         $code
 * @property Carbon      $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class FirmwareFeature extends Model implements IFirmwareFeature
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    public static function newFactory(): FirmwareFeatureFactory
    {
        return FirmwareFeatureFactory::new();
    }

    public static function ensureId(int|IFirmwareFeature $value): int
    {
        return $value instanceof IFirmwareFeature ? $value->getId() : $value;
    }

    public static function assignCode(IFirmware $firmware, string $name): int
    {
        $firmwareIds = Firmware::query()
            ->where('name', $firmware->getName())
            ->pluck('id');

        /**
         * @var FirmwareFeature|null
         */
        $previous = FirmwareFeature::query()->whereIn('firmware_id', $firmwareIds)->first();
        if ($previous) {
            return $previous->code;
        }
        $code = FirmwareFeature::query()->whereIn('firmware_id', $firmwareIds)->max('code') ?? 0;

        return $code + 1;
    }

    protected $table = 'iot_server_firmwares_features';
    protected $fillable = [
        'firmware_id',
        'name',
        'code',
    ];

    public function firmware(): BelongsTo
    {
        return $this->belongsTo(Firmware::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirmwareId(): int
    {
        return $this->firmware_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function isSoftDeleted(): bool
    {
        return $this->trashed();
    }
}
