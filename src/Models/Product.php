<?php

namespace YektaSmart\IotServer\Models;

use dnj\AAA\Models\User;
use dnj\ErrorTracker\Laravel\Server\Models\App;
use dnj\UserLogger\Concerns\Loggable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use YektaSmart\IotServer\Contracts\IDeviceHandler;
use YektaSmart\IotServer\Contracts\IFrameware;
use YektaSmart\IotServer\Contracts\IProduct;
use YektaSmart\IotServer\Database\Factories\ProductFactory;
use YektaSmart\IotServer\Models\Concerns\HasOwner;
use YektaSmart\IotServer\Models\Concerns\HasSemVer;

/**
 * @property int                                     $id
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
 * @property Collection<Frameware>                   $framewares
 */
class Product extends Model implements IProduct
{
    use HasFactory;
    use HasSemVer;
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

    public function framewares(): BelongsToMany
    {
        return $this->belongsToMany(Frameware::class, 'iot_server_products_framewares');
    }

    public function error_tracker_app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }

    public function getId(): int
    {
        return $this->id;
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

    public function getDefaultFeatureIds(int|IFrameware $frameware): ?array
    {
        if (null === $this->features) {
            return null;
        }
        if ($frameware instanceof IFrameware) {
            $frameware = $frameware->getId();
        }

        return $this->features[$frameware] ?? null;
    }

    public function getFramewareIds(): array
    {
        return $this->framewares->pluck('id')->all();
    }

    public function getErrorTrackerAppId(): int
    {
        return $this->error_tracker_app_id;
    }
}
