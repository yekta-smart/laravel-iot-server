<?php

namespace YektaSmart\IotServer\Rules;

use dnj\AAA\Rules\OwnerableModelExists;
use YektaSmart\IotServer\Contracts\IFirmwareManager;

class FirmwareExists extends OwnerableModelExists
{
    public function __construct(IFirmwareManager $manager)
    {
        parent::__construct($manager);
    }
}
