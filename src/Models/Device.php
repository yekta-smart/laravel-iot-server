<?php

namespace YektaSmart\IotServer\Models;

use Carbon\Carbon;
use dnj\AAA\HasOwner;
use dnj\AAA\Models\User;
use dnj\ErrorTracker\Laravel\Server\Models\Device as ErrorTrackerDevice;
use dnj\UserLogger\Concerns\Loggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use YektaSmart\IotServer\Contracts\IDevice;
use YektaSmart\IotServer\Contracts\IDeviceHandler;
use YektaSmart\IotServer\Database\Factories\DeviceFactory;
use YektaSmart\IotServer\UserUtil;

/**
 * @property int                                                                                            $id
 * @property int                                                                                            $owner_id
 * @property User                                                                                           $owner
 * @property string                                                                                         $title
 * @property int                                                                                            $product_id
 * @property Product                                                                                        $product
 * @property int                                                                                            $hardware_id
 * @property Hardware                                                                                       $hardware
 * @property int                                                                                            $firmware_id
 * @property Firmware                                                                                       $firmware
 * @property array{config:array{count:int|null,age:int|null},state:array{count:int|null,age:int|null}}|null $history_limits
 * @property array{enabledIds:int[],disabledIds:int[]}|null                                                 $features
 * @property int                                                                                            $error_tracker_device_id
 * @property ErrorTrackerDevice                                                                             $error_tracker_device
 * @property Carbon                                                                                         $created_at
 * @property Carbon|null                                                                                    $updated_at
 * @property Collection<DeviceConfig>                                                                       $configs
 * @property Collection<DeviceState>                                                                        $states
 */
class Device extends Model implements IDevice
{
    use HasOwner;
    use Loggable;
    use HasFactory;

    public static function newFactory(): DeviceFactory
    {
        return DeviceFactory::new();
    }

    public static function ensureId(int|IDevice $value): int
    {
        return $value instanceof IDevice ? $value->getId() : $value;
    }

    protected $table = 'iot_server_devices';
    protected $fillable = [
        'owner_id',
        'serial',
        'title',
        'product_id',
        'hardware_id',
        'firmware_id',
        'features',
        'error_tracker_device_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function hardware(): BelongsTo
    {
        return $this->belongsTo(Hardware::class);
    }

    public function firmware(): BelongsTo
    {
        return $this->belongsTo(Firmware::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'iot_server_devices_users');
    }

    public function errorTrackerDevice(): BelongsTo
    {
        return $this->belongsTo(ErrorTrackerDevice::class);
    }

    public function configs(): HasMany
    {
        return $this->hasMany(DeviceConfig::class);
    }

    public function states(): HasMany
    {
        return $this->hasMany(DeviceState::class);
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        if (isset($filters['title'])) {
            $query->where('title', 'LIKE', '%'.$filters['title'].'%');
        }
        if (isset($filters['product'])) {
            $query->where('product_id', Product::ensureId($filters['product']));
        }
        if (isset($filters['hardware'])) {
            $query->where('hardware_id', Hardware::ensureId($filters['hardware']));
        }
        if (isset($filters['firmware'])) {
            $query->where('firmware_id', Firmware::ensureId($filters['firmware']));
        }
        if (isset($filters['owner'])) {
            $query->where($this->getOwnerUserColumn(), UserUtil::ensureId($filters['owner']));
        }
        if (isset($filters['userHasAccess'])) {
            $this->scopeUserHasAccess($query, $filters['userHasAccess']);
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSerial(): string
    {
        return $this->serial;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUserIds(): array
    {
        return $this->users->pluck('id')->all();
    }

    public function getHistoryLimits(): ?array
    {
        return $this->history_limits;
    }

    public function getProductId(): int
    {
        return $this->product_id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getHardwareId(): int
    {
        return $this->hardware_id;
    }

    public function getHardware(): Hardware
    {
        return $this->hardware;
    }

    public function getFirmwareId(): int
    {
        return $this->firmware_id;
    }

    public function getFirmware(): Firmware
    {
        return $this->firmware;
    }

    public function getFeaturesCustomization(): ?array
    {
        return $this->features;
    }

    public function getErrorTrackerDeviceId(): int
    {
        return $this->error_tracker_device_id;
    }

    public function getErrorTrackerDevice(): ErrorTrackerDevice
    {
        return $this->errorTrackerDevice;
    }

    public function getConfig(): ?DeviceConfig
    {
        return $this->configs()->getQuery()->orderBy('id', 'DESC')->first();
    }

    public function getState(): ?DeviceState
    {
        return $this->states()->getQuery()->orderBy('id', 'DESC')->first();
    }

    public function getHandler(): IDeviceHandler
    {
        return app($this->product->device_handler, [
            'device' => $this,
        ]);
    }
}
