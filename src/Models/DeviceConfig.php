<?php

namespace YektaSmart\IotServer\Models;

use Carbon\Carbon;
use dnj\AAA\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use YektaSmart\IotServer\Contracts\IDevice;
use YektaSmart\IotServer\Contracts\IDeviceConfig;
use YektaSmart\IotServer\Database\Factories\DeviceConfigFactory;

/**
 * @property int                      $id
 * @property int                      $device_id
 * @property Device                   $device
 * @property int|null                 $configurator_id
 * @property User|null                $configurator
 * @property array<string,mixed>|null $configurator_data
 * @property array<string,mixed>      $data
 * @property Carbon                   $created_at
 */
class DeviceConfig extends Model implements IDeviceConfig
{
    use HasFactory;

    public const UPDATED_AT = null;

    public static function newFactory(): DeviceConfigFactory
    {
        return DeviceConfigFactory::new();
    }

    public static function ensureId(int|IDeviceConfig $value): int
    {
        return $value instanceof IDeviceConfig ? $value->getId() : $value;
    }

    protected $table = 'iot_server_devices_configs';
    protected $fillable = [
        'device_id',
        'configurator_id',
        'configurator_data',
        'data',
        'created_at',
    ];

    protected $casts = [
        'configurator_data' => 'array',
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

    public function getConfiguratorId(): ?int
    {
        return $this->configurator_id;
    }

    public function getConfiguratorData(): ?array
    {
        return $this->configurator_data;
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
