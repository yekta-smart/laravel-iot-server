<?php

namespace YektaSmart\IotServer\Models;

use dnj\AAA\HasOwner;
use dnj\AAA\Models\User;
use dnj\ErrorTracker\Laravel\Server\Models\App;
use dnj\UserLogger\Concerns\Loggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use YektaSmart\IotServer\Contracts\IDeviceHandler;
use YektaSmart\IotServer\Contracts\IFirmware;
use YektaSmart\IotServer\Contracts\IProduct;
use YektaSmart\IotServer\Database\Factories\ProductFactory;

/**
 * @property int                                     $id
 * @property string                                  $serial
 * @property int                                     $owner_id
 * @property User                                    $owner
 * @property string                                  $title
 * @property class-string<IDeviceHandler>            $device_handler
 * @property array{count:int|null,age:int|null}|null $state_history_limits
 * @property int                                     $error_tracker_app_id
 * @property array<int,int[]>|null                   $features
 * @property Carbon                                  $created_at
 * @property Carbon|null                             $updated_at
 * @property App                                     $error_tracker_app
 * @property Collection<Hardware>                    $hardwares
 * @property Collection<Firmware>                    $firmwares
 */
class Product extends Model implements IProduct
{
    use HasFactory;
    use HasOwner;
    use Loggable;

    public static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }

    public static function ensureId(int|IProduct $value): int
    {
        return $value instanceof IProduct ? $value->getId() : $value;
    }

    protected $table = 'iot_server_products';
    protected $fillable = [
        'parent_id',
        'owner_id',
        'serial',
        'device_handler',
        'title',
    ];
    protected $casts = [
        'state_history_limits' => 'array',
        'features' => 'array',
    ];

    public function hardwares(): BelongsToMany
    {
        return $this->belongsToMany(Hardware::class, 'iot_server_hardwares_products');
    }

    public function firmwares(): BelongsToMany
    {
        return $this->belongsToMany(Firmware::class, 'iot_server_products_firmwares');
    }

    public function error_tracker_app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        if (isset($filters['serial'])) {
            $query->where('serial', $filters['serial']);
        }
        if (isset($filters['title'])) {
            $query->where('title', 'LIKE', '%'.$filters['title'].'%');
        }
        if (isset($filters['hardware'])) {
            $query->whereRelation('hardwares', 'id', 'IN', array_map(fn ($h) => Hardware::ensureId($h), $filters['hardware']));
        }
        if (isset($filters['firmware'])) {
            $query->whereRelation('firmwares', 'id', 'IN', array_map(fn ($h) => Firmware::ensureId($h), $filters['hardware']));
        }
        if (isset($filters['owner'])) {
            $query->where($this->getOwnerUserColumn(), User::ensureId($filters['owner']));
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

    public function getDeviceHandler(): string
    {
        return $this->device_handler;
    }

    public function getStateHistoryLimits(): ?array
    {
        return $this->state_history_limits;
    }

    public function getHardwareIds(): array
    {
        return $this->hardwares->pluck('id')->all();
    }

    public function getDefaultFeatureIds(int|IFirmware $firmware): ?array
    {
        if (null === $this->features) {
            return null;
        }
        if ($firmware instanceof IFirmware) {
            $firmware = $firmware->getId();
        }

        return $this->features[$firmware] ?? null;
    }

    public function getFirmwareIds(): array
    {
        return $this->firmwares->pluck('id')->all();
    }

    public function getErrorTrackerAppId(): int
    {
        return $this->error_tracker_app_id;
    }
}
