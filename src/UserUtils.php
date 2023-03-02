<?php

namespace YektaSmart\IotServer;

use Illuminate\Contracts\Auth\Authenticatable;

class UserUtil
{
    public static function ensureId(Authenticatable|int $u): int
    {
        return $u instanceof Authenticatable ? $u->getAuthIdentifier() : $u;
    }
}
