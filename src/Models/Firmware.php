<?php

namespace YektaSmart\IotServer\Models;

use dnj\AAA\HasOwner;
use dnj\AAA\Models\User;
use dnj\ErrorTracker\Laravel\Server\Models\App;
use dnj\Filesystem\Contracts\IFile;
use dnj\UserLogger\Concerns\Loggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use YektaSmart\IotServer\Casts\File;
use YektaSmart\IotServer\Contracts\IFirmware;
use YektaSmart\IotServer\Database\Factories\FirmwareFactory;
use YektaSmart\IotServer\Models\Concerns\HasVersion;

/**
 * @property int                  $id
 * @property int                  $owner_id
 * @property User                 $owner
 * @property string               $name
 * @property IFile                $file
 * @property Collection<Hardware> $hardwares
 * @property Collection<Firmware> $firmwares
 * @property Collection<Features> $features
 */
class Firmware extends Model implements IFirmware
{
    use HasFactory;
    use HasVersion;
    use HasOwner;
    use Loggable;

    public static function newFactory(): FirmwareFactory
    {
        return FirmwareFactory::new();
    }

    public static function ensureId(int|IFirmware $value): int
    {
        return $value instanceof IFirmware ? $value->getId() : $value;
    }

    protected $table = 'iot_server_firmwares';
    protected $fillable = [
        'owner_id',
        'serial',
        'name',
        'file',
        'version',
    ];

    protected $casts = [
        'file' => File::class,
    ];

    public function hardwares(): BelongsToMany
    {
        return $this->belongsToMany(Hardware::class, 'iot_server_hardwares_firmwares');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'iot_server_products_firmwares');
    }

    public function features(): HasMany
    {
        return $this->hasMany(FirmwareFeature::class);
    }

    public function error_tracker_app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        if (isset($filters['name'])) {
            $query->where('name', 'LIKE', '%'.$filters['name'].'%');
        }
        if (isset($filters['compatibleWithHardware'])) {
            $query->whereRelation('hardwares', 'id', Hardware::ensureId($filters['compatibleWithHardware']));
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getFile(): IFile
    {
        return $this->file;
    }

    public function getHardwareIds(): array
    {
        return $this->hardwares->pluck('id')->all();
    }

    public function getProductIds(): array
    {
        return $this->products->pluck('id')->all();
    }
}
