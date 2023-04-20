<?php

namespace YektaSmart\IotServer\Http\Requests;

use dnj\AAA\Rules\UserExists;
use Illuminate\Foundation\Http\FormRequest;
use YektaSmart\IotServer\Contracts\IDeviceHandler;
use YektaSmart\IotServer\Contracts\IProduct;
use YektaSmart\IotServer\Rules\ProductDeviceHandler;
use YektaSmart\IotServer\Rules\ProductSerialBeUnique;

/**
 * @property string                       $title
 * @property class-string<IDeviceHandler> $deviceHandler
 * @property int|null                     $owner
 * @property string                       $serial
 */
class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', IProduct::class);
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'title' => ['required', 'string'],
            'deviceHandler' => ['required', 'string', new ProductDeviceHandler()],
            'owner' => ['required', 'int', app(UserExists::class)->userHasAccess($user)],
            'serial' => ['required', 'sometimes', 'ascii', 'size:32', app(ProductSerialBeUnique::class)],
        ];
    }
}
