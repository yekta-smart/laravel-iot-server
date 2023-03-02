<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use YektaSmart\IotServer\Models\Hardware;
use YektaSmart\IotServer\Models\Product;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('iot_server_hardwares_products', function (Blueprint $table) {
            $table->foreignIdFor(Hardware::class, 'hardware_id');
            $table->foreignIdFor(Product::class, 'product_id');
            $table->primary(['hardware_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_server_hardwares_products');
    }
};
