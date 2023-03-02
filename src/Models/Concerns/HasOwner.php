<?php

namespace YektaSmart\IotServer\Models\Concerns;

use dnj\AAA\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasOwner
{
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getOwnerUserId(): int
    {
        return $this->owner_id;
    }

    public function getOwnerUserColumn(): string
    {
        return 'owner_id';
    }
}
