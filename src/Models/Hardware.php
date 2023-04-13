<?php

namespace YektaSmart\IotServer\Models;

use Carbon\Carbon;
use dnj\AAA\HasOwner;
use dnj\AAA\Models\User;
use dnj\UserLogger\Concerns\Loggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use YektaSmart\IotServer\Contracts\IHardware;
use YektaSmart\IotServer\Database\Factories\HardwareFactory;
use YektaSmart\IotServer\Models\Concerns\HasVersion;

/**
 * @property int                  $id
 * @property int                  $owner_id
 * @property User                 $owner
 * @property string               $name
 * @property int                  $version
 * @property Carbon               $created_at
 * @property Carbon|null          $updated_at
 * @property Collection<Product>  $products
 * @property Collection<Firmware> $firmwares
 */
class Hardware extends Model implements IHardware
{
    use HasFactory;
    use HasVersion;
    use HasOwner;
    use Loggable;

    public static function newFactory(): HardwareFactory
    {
        return HardwareFactory::new();
    }

    public static function ensureId(int|IHardware $value): int
    {
        return $value instanceof IHardware ? $value->getId() : $value;
    }

    protected $table = 'iot_server_hardwares';
    protected $fillable = [
        'parent_id',
        'owner_id',
        'serial',
        'name',
        'version',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'iot_server_hardwares_products');
    }

    public function firmwares(): BelongsToMany
    {
        return $this->belongsToMany(Firmware::class, 'iot_server_hardwares_firmwares');
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        if (isset($filters['name'])) {
            $query->where('name', 'LIKE', '%'.$filters['name'].'%');
        }
        if (isset($filters['compatibleWithProduct'])) {
            $query->whereRelation('products', 'id', Product::ensureId($filters['compatibleWithProduct']));
        }
        if (isset($filters['compatibleWithFirmware'])) {
            $query->whereRelation('firmwares', 'id', Firmware::ensureId($filters['compatibleWithFirmware']));
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

    public function getProductIds(): array
    {
        return $this->products->pluck('id')->all();
    }

    public function getFirmwareIds(): array
    {
        return $this->firmwares()->pluck('id')->all();
    }
}
