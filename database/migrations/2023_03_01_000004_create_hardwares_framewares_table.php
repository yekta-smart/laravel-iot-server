<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use YektaSmart\IotServer\Models\Frameware;
use YektaSmart\IotServer\Models\Hardware;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('iot_server_hardwares_framewares', function (Blueprint $table) {
            $table->foreignIdFor(Hardware::class, 'hardware_id');
            $table->foreignIdFor(Frameware::class, 'frameware_id');
            $table->primary(['hardware_id', 'frameware_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_server_hardwares_framewares');
    }
};
