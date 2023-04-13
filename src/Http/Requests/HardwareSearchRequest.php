<?php

namespace YektaSmart\IotServer\Http\Requests;

use dnj\AAA\Rules\UserExists;
use Illuminate\Foundation\Http\FormRequest;
use YektaSmart\IotServer\Contracts\IHardware;
use YektaSmart\IotServer\Rules\FirmwareExists;
use YektaSmart\IotServer\Rules\ProductExists;

class HardwareSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', IHardware::class);
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'name' => ['required', 'sometimes', 'string'],
            'owner' => ['required', 'sometimes', app(UserExists::class)->userHasAccess($user)],
            'compatibleWithProduct' => ['required', 'sometimes', 'array'],
            'compatibleWithProduct.*' => ['required', 'int', app(ProductExists::class)->userHasAccess($user)],
            'compatibleWithFirmware' => ['required', 'sometimes', 'array'],
            'compatibleWithFirmware.*' => ['required', 'int', app(FirmwareExists::class)->userHasAccess($user)],
        ];
    }
}
