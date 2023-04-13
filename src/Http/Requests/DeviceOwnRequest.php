<?php

namespace YektaSmart\IotServer\Http\Requests;

use dnj\AAA\Rules\UserExists;
use Illuminate\Foundation\Http\FormRequest;
use YektaSmart\IotServer\Contracts\IDevice;

/**
 * @property string      $serial
 * @property string|null $title
 * @property int|null    $owner
 */
class DeviceOwnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('own', IDevice::class);
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'serial' => ['required',  'string', 'ascii', 'size:32'],
            'title' => ['required', 'sometimes', 'string'],
            'owner' => ['required', 'sometimes', 'int', app(UserExists::class)->userHasAccess($user)],
        ];
    }
}
