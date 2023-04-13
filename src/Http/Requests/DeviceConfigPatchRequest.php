<?php

namespace YektaSmart\IotServer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use YektaSmart\IotServer\Contracts\IDeviceManager;

/**
 * @property string|null $title
 * @property int|null    $product
 * @property int|null    $hardware
 * @property int|null    $firmware
 * @property int|null    $owner
 * @property string|null $serial
 */
class DeviceConfigPatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        $device = app(IDeviceManager::class)->findOrFail($this->route('device'));

        return $this->user()->can('configPatch', $device);
    }

    public function rules(): array
    {
        return [
            'config' => ['required'],
        ];
    }
}
