<?php

use YektaSmart\IotServer\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;
use YektaSmart\IotServer\Http\Controllers\HardwareController;
use YektaSmart\IotServer\Http\Controllers\ProductController;

Route::middleware(["api", "auth"])->group(function() {
	Route::apiResources([
		"devices" => DeviceController::class,
		"hardwares" => HardwareController::class,
		"products" => ProductController::class,
	]);
	Route::post("devices/{device}/disown", [DeviceController::class, "disown"])->name("devices.disown");
	Route::post("devices/own", [DeviceController::class, "own"])->name("devices.own");
});