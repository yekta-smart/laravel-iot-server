<?php

namespace YektaSmart\IotServer\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use YektaSmart\IotServer\Contracts\IDeviceManager;
use YektaSmart\IotServer\Http\Requests\DeviceOwnRequest;
use YektaSmart\IotServer\Http\Requests\DeviceSearchRequest;
use YektaSmart\IotServer\Http\Requests\DeviceStoreRequest;
use YektaSmart\IotServer\Http\Requests\DeviceUpdateRequest;
use YektaSmart\IotServer\Http\Resources\DeviceResource;
use YektaSmart\IotServer\Models\Device;

class DeviceController extends Controller
{
    public function __construct(protected IDeviceManager $manager)
    {
    }

    public function index(DeviceSearchRequest $request): AnonymousResourceCollection
    {
        return DeviceResource::collection(
            Device::query()
                ->userHasAccess($request->user())
                ->filter($request->validated())
                ->cursorPaginate()
        );
    }

    public function show(int $device): DeviceResource
    {
        $device = $this->manager->findOrFail($device);
        $this->authorize('view', $device);

        return DeviceResource::make($device)->withConfig()->withState();
    }

    public function store(DeviceStoreRequest $request): DeviceResource
    {
        $device = $this->manager->store($request->title, $request->product, $request->hardware, $request->firmware, $request->owner, [], null, null, $request->serial, true);

        return DeviceResource::make($device);
    }

    public function update(DeviceUpdateRequest $request, int $device): DeviceResource
    {
        $changes = $request->validated();
        $device = $this->manager->update($device, $changes, true);

        return DeviceResource::make($device);
    }

    public function destroy(int $device): Response
    {
        $device = $this->manager->findOrFail($device);
        $this->authorize('delete', $device);

        $this->manager->destroy($device, true);

        return response()->noContent();
    }

    public function disown(int $device): Response
    {
        $device = $this->manager->findOrFail($device);
        $this->authorize('disown', $device);
        $this->manager->update($device, [
            'owner_id' => null,
        ], true);

        return response()->noContent();
    }

    public function own(DeviceOwnRequest $request): DeviceResource
    {
        $device = $this->manager->findBySerialOrFail($request->serial);
        $changes = [
            'owner' => $request->owner ?? $request->user(),
        ];
        if ($request->title) {
            $changes['title'] = $request->title;
        }
        $device = $this->manager->update($device, $changes, true);

        return DeviceResource::make($device);
    }
}
