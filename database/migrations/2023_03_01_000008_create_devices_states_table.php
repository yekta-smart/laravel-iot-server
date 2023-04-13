<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use YektaSmart\IotServer\Models\Device;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('iot_server_devices_states', function (Blueprint $table) {
            $table->id();

            $table->foreignId('device_id')
                ->constrained((new Device())->getTable(), 'id')
                ->cascadeOnDelete();

            $table->timestamp('created_at');

            $table->json('data');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_server_devices_states');
    }
};
