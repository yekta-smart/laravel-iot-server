<?php

namespace YektaSmart\IotServer\Policies;

use dnj\AAA\Policy;
use YektaSmart\IotServer\Contracts\IProduct;

class ProductPolicy extends Policy
{
    public function getModel(): string
    {
        return IProduct::class;
    }
}
