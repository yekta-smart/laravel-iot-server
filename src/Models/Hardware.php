<?php

namespace YektaSmart\IotServer\Models;

use Carbon\Carbon;
use dnj\AAA\Models\User;
use dnj\UserLogger\Concerns\Loggable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use YektaSmart\IotServer\Contracts\IHardware;
use YektaSmart\IotServer\Database\Factories\HardwareFactory;
use YektaSmart\IotServer\Models\Concerns\HasOwner;
use YektaSmart\IotServer\Models\Concerns\HasSemVer;

/**
 * @property int                   $id
 * @property int                   $owner_id
 * @property User                  $owner
 * @property string                $name
 * @property int                   $version
 * @property Carbon                $created_at
 * @property Carbon|null           $updated_at
 * @property Collection<Product>   $products
 * @property Collection<Frameware> $framewares
 */
class Hardware extends Model implements IHardware
{
    use HasFactory;
    use HasSemVer;
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
        'title',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'iot_server_hardwares_products');
    }

    public function framewares(): BelongsToMany
    {
        return $this->belongsToMany(Frameware::class, 'iot_server_hardwares_framewares');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProductIds(): array
    {
        return $this->products->pluck('id')->all();
    }

    public function getFramewareIds(): array
    {
        return $this->framewares()->pluck('id')->all();
    }
}
