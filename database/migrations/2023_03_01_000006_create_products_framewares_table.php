<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use YektaSmart\IotServer\Models\Firmware;
use YektaSmart\IotServer\Models\Product;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('iot_server_products_firmwares', function (Blueprint $table) {
            $table->foreignId('product_id')
                ->constrained((new Product())->getTable(), 'id');

            $table->foreignId('firmware_id')
                ->constrained((new Firmware())->getTable(), 'id');

            $table->json('features')->nullable();

            $table->primary(['product_id', 'firmware_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_server_products_firmwares');
    }
};
