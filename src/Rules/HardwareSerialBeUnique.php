<?php

namespace YektaSmart\IotServer\Rules;

use Illuminate\Contracts\Validation\InvokableRule;
use YektaSmart\IotServer\Contracts\IHardwareManager;

class HardwareSerialBeUnique implements InvokableRule
{
    protected ?int $currentId = null;

    public function __construct(protected IHardwareManager $manager)
    {
    }

    public function __invoke($attribute, $value, $fail)
    {
        $model = $this->manager->findBySerial($value);
        if ($model and $model->getId() != $this->currentId) {
            $fail('The :attribute is already taken');
        }
    }

    /**
     * @return $this
     */
    public function setCurrent(?int $currentId): static
    {
        $this->currentId = $currentId;

        return $this;
    }
}
