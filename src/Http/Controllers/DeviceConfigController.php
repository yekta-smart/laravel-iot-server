<?php
namespace YektaSmart\IotServer\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use YektaSmart\IotServer\Http\Requests\DeviceConfigPatchRequest;

class DeviceConfigController extends Controller {
	public function patch(DeviceConfigPatchRequest $request, int $device) {
		Redis::publish("test-channel", json_encode("hi"));
		Redis::subscribe("test-channel")
	}
}