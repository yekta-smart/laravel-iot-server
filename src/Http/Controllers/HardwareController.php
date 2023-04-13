<?php

namespace YektaSmart\IotServer\Http\Controllers;

use YektaSmart\IotServer\Contracts\IHardwareManager;
use YektaSmart\IotServer\Http\Requests\HardwareSearchRequest;
use YektaSmart\IotServer\Http\Requests\HardwareStoreRequest;
use YektaSmart\IotServer\Http\Requests\HardwareUpdateRequest;
use YektaSmart\IotServer\Http\Resources\HardwareResource;
use YektaSmart\IotServer\Models\Hardware;

class HardwareController extends Controller
{
    public function __construct(protected IHardwareManager $manager)
    {
    }

    public function index(HardwareSearchRequest $request)
    {
        return Hardware::query()
            ->userHasAccess($request->user())
            ->filter($request->validated())
            ->cursorPaginate();
    }

    public function show(int $hardware)
    {
        $hardware = $this->manager->findOrFail($hardware);
        $this->authorize('view', $hardware);

        return HardwareResource::make($hardware);
    }

    public function store(HardwareStoreRequest $request)
    {
        $hardware = $this->manager->store($request->name, $request->version, $request->owner, [], [], $request->serial, true);

        return HardwareResource::make($hardware);
    }

    public function update(HardwareUpdateRequest $request, int $hardware)
    {
        $changes = $request->validated();
        $hardware = $this->manager->update($hardware, $changes, true);

        return HardwareResource::make($hardware);
    }

    public function destroy(int $hardware)
    {
        $hardware = $this->manager->findOrFail($hardware);
        $this->authorize('delete', $hardware);

        $this->manager->destroy($hardware, true);

        return response()->noContent();
    }
}
