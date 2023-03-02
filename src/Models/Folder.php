<?php

namespace YektaSmart\IotServer\Models;

use dnj\AAA\Models\User;
use dnj\UserLogger\Concerns\Loggable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use YektaSmart\IotServer\Contracts\IFolder;
use YektaSmart\IotServer\Database\Factories\FolderFactory;
use YektaSmart\IotServer\Models\Concerns\HasOwner;
use YektaSmart\IotServer\UserUtil;

/**
 * @property int                $id
 * @property int|null           $parent_id
 * @property static             $parent
 * @property int                $owner_id
 * @property User               $owner
 * @property string             $title
 * @property Carbon             $created_at
 * @property Carbon|null        $updated_at
 * @property Collection<Device> $devices
 * @property Collection<static> $folders
 * @property Collection<User>   $users
 */
class Folder extends Model implements IFolder
{
    use HasFactory;
    use HasOwner;
    use Loggable;

    public static function newFactory(): FolderFactory
    {
        return FolderFactory::new();
    }

    public static function ensureId(int|IFolder $value): int
    {
        return $value instanceof IFolder ? $value->getId() : $value;
    }

    protected $table = 'iot_server_folders';
    protected $fillable = [
        'parent_id',
        'owner_id',
        'title',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class);
    }

    public function folders(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'iot_server_folders_devices');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'iot_server_folders_users');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    public function getUserIds(): array
    {
        return $this->users->pluck('id')->all();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDeviceIds(): array
    {
        return $this->devices->pluck('id')->all();
    }

    public function getFolderIds(): array
    {
        return $this->folders->pluck('id')->all();
    }

    public function canChangeOwnerTo(int|Authenticatable $other): bool
    {
        return $this->owner_id === UserUtil::ensureId($other);
    }

    public function canChangeParentTo(int|IFolder $other): bool
    {
        if (is_int($other)) {
            /**
             * @var self
             */
            $other = self::query()->findOrFail($other);
        }
        if ($other->getOwnerUserId() != $this->getOwnerUserId()) {
            return false;
        }

        return $this->isChildOfMine($other);
    }

    public function isChildOfMine(int|IFolder $other, bool $direct = false): bool
    {
        if (is_int($other)) {
            /**
             * @var self
             */
            $other = self::query()->findOrFail($other);
        }
        if ($direct) {
            return $other->getOwnerUserId() == $this->id;
        }

        $o = $other;
        while (null !== $o and $o->getParentId() != $this->id) {
            $o = self::query()->findOrFail($o->getParentId());
        }

        return null === $o;
    }
}
