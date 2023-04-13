<?php

namespace YektaSmart\IotServer\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use YektaSmart\IotServer\Contracts\IDevice;
use YektaSmart\IotServer\Contracts\IDeviceState;
use YektaSmart\IotServer\Database\Factories\DeviceStateFactory;

/**
 * @property int                 $id
 * @property int                 $device_id
 * @property Device              $device
 * @property array<string,mixed> $data
 * @property Carbon              $created_at
 */
class DeviceState extends Model implements IDeviceState
{
    use HasFactory;

    public const UPDATED_AT = null;

    public static function newFactory(): DeviceStateFactory
    {
        return DeviceStateFactory::new();
    }

    public static function ensureId(int|IDeviceState $value): int
    {
        return $value instanceof IDeviceState ? $value->getId() : $value;
    }

    protected $table = 'iot_server_devices_states';
    protected $fillable = [
        'device_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function scopeForDevice(Builder $builder, int|IDevice $device): void
    {
        $builder->where('device_id', self::ensureId($device));
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDeviceId(): int
    {
        return $this->device_id;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
