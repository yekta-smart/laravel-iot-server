<?php

use dnj\AAA\Models\User;
use dnj\ErrorTracker\Laravel\Server\Models\Device;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use YektaSmart\IotServer\Models\Frameware;
use YektaSmart\IotServer\Models\Hardware;
use YektaSmart\IotServer\Models\Product;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('iot_server_devices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignIdFor(User::class, 'owner_id');
            $table->json('history_limits')->collation('latin1_general_ci')->nullable();
            $table->foreignIdFor(Product::class, 'product_id');
            $table->foreignIdFor(Frameware::class, 'frameware_id');
            $table->foreignIdFor(Hardware::class, 'hardware_id');
            $table->json('features')->collation('latin1_general_ci')->nullable();
            $table->foreignIdFor(Device::class, 'error_tracker_device_id')->unique();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_server_devices');
    }
};
