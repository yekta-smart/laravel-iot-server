<?php

use dnj\AAA\Models\User;
use dnj\ErrorTracker\Laravel\Server\Models\Device;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use YektaSmart\IotServer\Models\Firmware;
use YektaSmart\IotServer\Models\Hardware;
use YektaSmart\IotServer\Models\Product;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('iot_server_devices', function (Blueprint $table) {
            $table->id();

            $table->string('serial', 32)
                ->collation('latin1_general_cs')
                ->unique();

            $table->string('title');

            $table->foreignId('owner_id')
                ->nullable()
                ->constrained((new User())->getTable(), 'id');

            $table->json('history_limits')
                ->nullable();

            $table->foreignId('product_id')
                ->constrained((new Product())->getTable(), 'id');

            $table->foreignId('firmware_id')
                ->constrained((new Firmware())->getTable(), 'id');

            $table->foreignId('hardware_id')
                ->constrained((new Hardware())->getTable(), 'id');

            $table->json('features')
                ->nullable();

            $table->foreignId('error_tracker_device_id')
                ->unique()
                ->constrained((new Device())->getTable(), 'id');

            $table->timestamp('created_at');

            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_server_devices');
    }
};
