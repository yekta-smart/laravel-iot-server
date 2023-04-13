<?php

namespace YektaSmart\IotServer\Http\Requests;

use dnj\AAA\Rules\UserExists;
use Illuminate\Foundation\Http\FormRequest;
use YektaSmart\IotServer\Contracts\IDevice;
use YektaSmart\IotServer\Rules\FirmwareExists;
use YektaSmart\IotServer\Rules\HardwareExists;
use YektaSmart\IotServer\Rules\ProductExists;

class DeviceSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', IDevice::class);
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'title' => ['required', 'sometimes', 'string'],
            'product' => ['required', 'sometimes', 'int', app(ProductExists::class)],
            'hardware' => ['required', 'sometimes', 'int', app(HardwareExists::class)],
            'firmware' => ['required', 'sometimes', 'int', app(FirmwareExists::class)],
            'owner' => ['required', 'sometimes', 'int', app(UserExists::class)->userHasAccess($user)],
        ];
    }
}
