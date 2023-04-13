<?php

namespace YektaSmart\IotServer\Rules;

use dnj\AAA\Rules\OwnerableModelExists;
use YektaSmart\IotServer\Contracts\IHardwareManager;

class HardwareExists extends OwnerableModelExists
{
    public function __construct(IHardwareManager $manager)
    {
        parent::__construct($manager);
    }
}
