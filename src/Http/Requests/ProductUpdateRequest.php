<?php

namespace YektaSmart\IotServer\Http\Requests;

use dnj\AAA\Rules\UserExists;
use Illuminate\Foundation\Http\FormRequest;
use YektaSmart\IotServer\Contracts\IDeviceHandler;
use YektaSmart\IotServer\Contracts\IProductManager;
use YektaSmart\IotServer\Rules\ProductDeviceHandler;

/**
 * @property string|null                       $title
 * @property class-string<IDeviceHandler>|null $deviceHandler
 * @property int|null                          $owner
 */
class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $product = app(IProductManager::class)->findOrFail($this->route('product'));

        return $this->user()->can('update', $product);
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'title' => ['required', 'sometimes', 'string'],
            'deviceHandler' => ['required', 'sometimes', 'string', new ProductDeviceHandler()],
            'owner' => ['required', 'sometimes', 'int', app(UserExists::class)->userHasAccess($user)],
        ];
    }
}
