<?php

namespace YektaSmart\IotServer\Models\Concerns;

trait HasSemVer
{
    public function getSemVer(): string
    {
        $version = $this->version;

        return ($version & 1024).'.'.(($version >> 10) & 1023).'.'.(($version >> 20) & 1023);
    }

    public function getSemVerInt(): int
    {
        return $this->version;
    }
}
