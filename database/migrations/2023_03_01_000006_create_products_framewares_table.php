<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use YektaSmart\IotServer\Models\Frameware;
use YektaSmart\IotServer\Models\Product;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('iot_server_products_framewares', function (Blueprint $table) {
            $table->foreignIdFor(Product::class, 'product_id');
            $table->foreignIdFor(Frameware::class, 'frameware_id');
            $table->json('features')->collation('latin1_general_ci')->nullable();
            $table->primary(['product_id', 'frameware_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_server_products_framewares');
    }
};
