<?php

namespace YektaSmart\IotServer\Models;

use dnj\AAA\Models\User;
use dnj\ErrorTracker\Laravel\Server\Models\App;
use dnj\Filesystem\Contracts\IFile;
use dnj\UserLogger\Concerns\Loggable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use YektaSmart\IotServer\Contracts\IFrameware;
use YektaSmart\IotServer\Database\Factories\FramewareFactory;
use YektaSmart\IotServer\Models\Concerns\HasOwner;
use YektaSmart\IotServer\Models\Concerns\HasSemVer;

/**
 * @property int                   $id
 * @property int                   $owner_id
 * @property User                  $owner
 * @property string                $name
 * @property IFile                 $file
 * @property Collection<Hardware>  $hardwares
 * @property Collection<Frameware> $framewares
 * @property Collection<Features>  $features
 */
class Frameware extends Model implements IFrameware
{
    use HasFactory;
    use HasSemVer;
    use HasOwner;
    use Loggable;

    public static function newFactory(): FramewareFactory
    {
        return FramewareFactory::new();
    }

    public static function ensureId(int|IFrameware $value): int
    {
        return $value instanceof IFrameware ? $value->getId() : $value;
    }

    protected $table = 'iot_server_framewares';
    protected $fillable = [
        'owner_id',
        'name',
        'file',
    ];

    public function hardwares(): BelongsToMany
    {
        return $this->belongsToMany(Hardware::class, 'iot_server_hardwares_framewares');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'iot_server_products_framewares');
    }

    public function features(): HasMany
    {
        return $this->hasMany(FramewareFeature::class);
    }

    public function error_tracker_app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFile(): IFile
    {
        return $this->file;
    }

    public function getStateHistoryLimits(): ?array
    {
        return $this->state_history_limits;
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
