<?php

namespace YektaSmart\IotServer\Http\Requests;

use dnj\AAA\Rules\UserExists;
use Illuminate\Foundation\Http\FormRequest;
use YektaSmart\IotServer\Contracts\IProduct;
use YektaSmart\IotServer\Rules\FirmwareExists;
use YektaSmart\IotServer\Rules\HardwareExists;

class ProductSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', IProduct::class);
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'serial' => ['required', 'sometimes', 'string'],
            'title' => ['required', 'sometimes', 'string'],
            'owner' => ['required', 'sometimes', app(UserExists::class)->userHasAccess($user)],
            'firmware' => ['required', 'sometimes', 'array'],
            'firmware.*' => ['required', 'int', app(FirmwareExists::class)],
            'hardware' => ['required', 'sometimes', 'array'],
            'hardware.*' => ['required', 'int', app(HardwareExists::class)],
        ];
    }
}
