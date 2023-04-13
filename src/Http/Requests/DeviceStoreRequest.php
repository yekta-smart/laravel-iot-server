<?php

namespace YektaSmart\IotServer\Http\Requests;

use dnj\AAA\Rules\UserExists;
use Illuminate\Foundation\Http\FormRequest;
use YektaSmart\IotServer\Contracts\IDevice;
use YektaSmart\IotServer\Rules\DeviceSerialBeUnique;
use YektaSmart\IotServer\Rules\FirmwareExists;
use YektaSmart\IotServer\Rules\HardwareExists;
use YektaSmart\IotServer\Rules\ProductExists;

/**
 * @property string      $title
 * @property int         $product
 * @property int         $hardware
 * @property int         $firmware
 * @property int         $owner
 * @property string|null $serial
 */
class DeviceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', IDevice::class);
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'title' => ['required', 'string'],
            'product' => ['required', 'int', app(ProductExists::class)->userHasAccess($user)],
            'hardware' => ['required', 'int', app(HardwareExists::class)->userHasAccess($user)],
            'firmware' => ['required', 'int', app(FirmwareExists::class)->userHasAccess($user)],
            'owner' => ['required', 'int', app(UserExists::class)->userHasAccess($user)],
            'serial' => ['required', 'sometimes', 'ascii', 'size:32', app(DeviceSerialBeUnique::class)],
        ];
    }
}
