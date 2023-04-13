<?php

namespace YektaSmart\IotServer\Http\Controllers;

use YektaSmart\IotServer\Contracts\IProductManager;
use YektaSmart\IotServer\Http\Requests\ProductSearchRequest;
use YektaSmart\IotServer\Http\Requests\ProductStoreRequest;
use YektaSmart\IotServer\Http\Requests\ProductUpdateRequest;
use YektaSmart\IotServer\Http\Resources\ProductResource;
use YektaSmart\IotServer\Models\Product;

class ProductController extends Controller
{
    public function __construct(protected IProductManager $manager)
    {
    }

    public function index(ProductSearchRequest $request)
    {
        return Product::query()
            ->userHasAccess($request->user())
            ->filter($request->validated())
            ->cursorPaginate();
    }

    public function show(int $product)
    {
        $product = $this->manager->findOrFail($product);
        $this->authorize('view', $product);

        return ProductResource::make($product);
    }

    public function store(ProductStoreRequest $request)
    {
        $product = $this->manager->store($request->title, $request->deviceHandler, $request->owner, [], [], null, true);

        return ProductResource::make($product);
    }

    public function update(ProductUpdateRequest $request, int $product)
    {
        $changes = $request->validated();
        $product = $this->manager->update($product, $changes, true);

        return ProductResource::make($product);
    }

    public function destroy(int $product)
    {
        $product = $this->manager->findOrFail($product);
        $this->authorize('delete', $product);

        $this->manager->destroy($product, true);

        return response()->noContent();
    }
}
