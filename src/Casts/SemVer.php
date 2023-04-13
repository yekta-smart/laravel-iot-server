<?php

namespace YektaSmart\IotServer\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class SemVer implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param mixed                               $value
     *
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        $major = $value >> 20 & 1023;
        $minor = $value >> 10 & 1023;
        $patch = $value & 1023;

        return "{$major}.{$minor}.{$patch}";
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param mixed                               $value
     *
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        $parts = explode('.', $value, 3);
        if (count($parts) < 3) {
            throw new \Exception();
        }
        $version = 0;
        for ($x = 0; $x < 3; ++$x) {
            $part = intval($parts[$x]);
            $version |= $part << ((2 - $x) * 10);
        }

        return $version;
    }
}
