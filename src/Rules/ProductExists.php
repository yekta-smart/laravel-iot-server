<?php

namespace YektaSmart\IotServer\Rules;

use dnj\AAA\Rules\OwnerableModelExists;
use YektaSmart\IotServer\Contracts\IProductManager;

class ProductExists extends OwnerableModelExists
{
    public function __construct(IProductManager $manager)
    {
        parent::__construct($manager);
    }
}
