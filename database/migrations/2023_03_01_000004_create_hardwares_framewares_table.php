<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use YektaSmart\IotServer\Models\Firmware;
use YektaSmart\IotServer\Models\Hardware;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('iot_server_hardwares_firmwares', function (Blueprint $table) {
            $table->foreignId('hardware_id')
                ->constrained((new Hardware())->getTable(), 'id');

            $table->foreignId('firmware_id')
                ->constrained((new Firmware())->getTable(), 'id');

            $table->primary(['hardware_id', 'firmware_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_server_hardwares_firmwares');
    }
};
