<?php

namespace YektaSmart\IotServer\Models\Concerns;

use YektaSmart\IotServer\Casts\SemVer;

trait HasVersion
{
    public function getVersion(): string
    {
        return $this->version;
    }

    public function getVersionInt(): int
    {
        return (new SemVer())->set($this, 'version', $this->version, []);
    }

    public function initializeHasVersion()
    {
        if (!isset($this->casts['version'])) {
            $this->casts['version'] = SemVer::class;
        }
    }
}
