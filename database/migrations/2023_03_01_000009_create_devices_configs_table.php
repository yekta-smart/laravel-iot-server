<?php

use dnj\AAA\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use YektaSmart\IotServer\Models\Device;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('iot_server_devices_configs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('device_id')
                ->constrained((new Device())->getTable(), 'id')
                ->cascadeOnDelete();

            $table->foreignId('configurator_id')
                ->nullable()
                ->constrained((new User())->getTable(), 'id');

            $table->json('configurator_data')->nullable();

            $table->timestamp('created_at');

            $table->json('data');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_server_devices_configs');
    }
};
