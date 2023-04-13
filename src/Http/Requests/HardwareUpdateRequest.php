<?php

namespace YektaSmart\IotServer\Http\Requests;

use dnj\AAA\Rules\UserExists;
use Illuminate\Foundation\Http\FormRequest;
use YektaSmart\IotServer\Contracts\IHardwareManager;
use YektaSmart\IotServer\Rules\HardwareSerialBeUnique;

/**
 * @property string|null $name
 * @property string|null $version
 * @property int|null    $owner
 * @property string|null $serial
 */
class HardwareUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $hardware = app(IHardwareManager::class)->findOrFail($this->route('hardware'));

        return $this->user()->can('update', $hardware);
    }

    public function rules(): array
    {
        $hardwareId = $this->route('hardware');
        $user = $this->user();

        return [
            'name' => ['required', 'sometimes', 'string'],
            'version' => ['required', 'sometimes', 'string'],
            'owner' => ['required', 'sometimes', 'int', app(UserExists::class)->userHasAccess($user)],
            'serial' => ['required', 'sometimes', 'ascii', 'size:32', app(HardwareSerialBeUnique::class)->setCurrent($hardwareId)],
        ];
    }
}
