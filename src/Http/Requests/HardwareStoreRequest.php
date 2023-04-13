<?php

namespace YektaSmart\IotServer\Http\Requests;

use dnj\AAA\Rules\UserExists;
use Illuminate\Foundation\Http\FormRequest;
use YektaSmart\IotServer\Contracts\IHardware;
use YektaSmart\IotServer\Rules\HardwareSerialBeUnique;

/**
 * @property string      $name
 * @property string      $version
 * @property int         $owner
 * @property string|null $serial
 */
class HardwareStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', IHardware::class);
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'name' => ['required', 'string'],
            'version' => ['required', 'string'],
            'owner' => ['required', 'int', app(UserExists::class)->userHasAccess($user)],
            'serial' => ['required', 'sometimes', 'ascii', 'size:32', app(HardwareSerialBeUnique::class)],
        ];
    }
}
