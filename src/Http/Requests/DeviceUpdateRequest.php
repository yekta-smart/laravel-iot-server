<?php

namespace YektaSmart\IotServer\Http\Requests;

use dnj\AAA\Rules\UserExists;
use Illuminate\Foundation\Http\FormRequest;
use YektaSmart\IotServer\Contracts\IDeviceManager;
use YektaSmart\IotServer\Rules\DeviceSerialBeUnique;
use YektaSmart\IotServer\Rules\FirmwareExists;
use YektaSmart\IotServer\Rules\HardwareExists;
use YektaSmart\IotServer\Rules\ProductExists;

/**
 * @property string|null $title
 * @property int|null    $product
 * @property int|null    $hardware
 * @property int|null    $firmware
 * @property int|null    $owner
 * @property string|null $serial
 */
class DeviceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $device = app(IDeviceManager::class)->findOrFail($this->route('device'));

        return $this->user()->can('update', $device);
    }

    public function rules(): array
    {
        $deviceId = $this->route('device');
        $user = $this->user();

        return [
            'title' => ['required', 'sometimes', 'string'],
            'product' => ['required', 'sometimes', 'int', app(ProductExists::class)->userHasAccess($user)],
            'hardware' => ['required', 'sometimes', 'int', app(HardwareExists::class)->userHasAccess($user)],
            'firmware' => ['required', 'sometimes', 'int', app(FirmwareExists::class)->userHasAccess($user)],
            'owner' => ['required', 'sometimes', 'int', app(UserExists::class)->userHasAccess($user)],
            'serial' => ['required', 'sometimes', 'ascii', 'size:32', app(DeviceSerialBeUnique::class)->setCurrent($deviceId)],
        ];
    }
}
