<?php

namespace YektaSmart\IotServer\Rules;

use Illuminate\Contracts\Validation\InvokableRule;
use YektaSmart\IotServer\Contracts\IDeviceHandler;

class ProductDeviceHandler implements InvokableRule
{
    public function __invoke($attribute, $value, $fail)
    {
        if (!is_string($value) or !class_exists($value) or !is_subclass_of($value, IDeviceHandler::class, true)) {
            $fail('The :attribute must be a valid class name');

            return;
        }
    }
}
