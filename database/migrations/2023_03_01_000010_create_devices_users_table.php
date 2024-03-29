<?php

use dnj\AAA\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use YektaSmart\IotServer\Models\Device;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('iot_server_devices_users', function (Blueprint $table) {
            $table->foreignId('device_id')
                ->constrained((new Device())->getTable(), 'id')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained((new User())->getTable(), 'id')
                ->cascadeOnDelete();

            $table->primary(['device_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_server_devices_users');
    }
};
