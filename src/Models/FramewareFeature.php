<?php

namespace YektaSmart\IotServer\Models;

use Carbon\Carbon;
use dnj\UserLogger\Concerns\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use YektaSmart\IotServer\Contracts\IFrameware;
use YektaSmart\IotServer\Contracts\IFramewareFeature;
use YektaSmart\IotServer\Database\Factories\FramewareFeatureFactory;

/**
 * @property int         $id
 * @property int         $frameware_id
 * @property Frameware   $frameware
 * @property string      $name
 * @property int         $code
 * @property Carbon      $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class FramewareFeature extends Model implements IFramewareFeature
{
    use HasFactory;
    use SoftDeletes;
    use Loggable;

    public static function newFactory(): FramewareFeatureFactory
    {
        return FramewareFeatureFactory::new();
    }

    public static function ensureId(int|IFramewareFeature $value): int
    {
        return $value instanceof IFramewareFeature ? $value->getId() : $value;
    }

    public static function assignCode(IFrameware $frameware, string $name): int
    {
        $framewareIds = Frameware::query()
            ->where('name', $frameware->getName())
            ->pluck('id');

        /**
         * @var FramewareFeature|null
         */
        $previous = FramewareFeature::query()->whereIn('frameware_id', $framewareIds)->first();
        if ($previous) {
            return $previous->code;
        }
        $code = FramewareFeature::query()->whereIn('frameware_id', $framewareIds)->max('code') ?? 0;

        return $code + 1;
    }

    protected $table = 'iot_server_framewares_features';
    protected $fillable = [
        'name',
        'code',
    ];

    public function frameware(): BelongsTo
    {
        return $this->belongsTo(Frameware::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFramewareId(): int
    {
        return $this->frameware_id;
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
