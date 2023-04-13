<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use YektaSmart\IotServer\Models\Device;
use YektaSmart\IotServer\Models\Folder;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('iot_server_folders_devices', function (Blueprint $table) {
            $table->foreignId('folder_id')
                ->constrained((new Folder())->getTable(), 'id')
                ->cascadeOnDelete();

            $table->foreignId('device_id')
                ->constrained((new Device())->getTable(), 'id')
                ->cascadeOnDelete();

            $table->primary(['folder_id', 'device_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_server_folders_devices');
    }
};
