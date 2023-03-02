<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use YektaSmart\IotServer\Models\Frameware;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('iot_server_framewares_features', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Frameware::class, 'frameware_id');
            $table->string('name', 255)->collation('latin1_general_ci')->unique();
            $table->unsignedInteger('code');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_server_framewares_features');
    }
};
