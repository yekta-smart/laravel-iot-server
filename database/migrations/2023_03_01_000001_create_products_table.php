<?php

use dnj\AAA\Models\User;
use dnj\ErrorTracker\Laravel\Server\Models\App;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('iot_server_products', function (Blueprint $table) {
            $table->id();

            $table->string('serial', 32)
                ->collation('latin1_general_ci')
                ->unique();

            $table->foreignId('owner_id')
                ->constrained((new User())->getTable(), 'id');

            $table->string('title');

            $table->string('device_handler')
                ->collation('latin1_general_ci');

            $table->foreignId('error_tracker_app_id')
                ->unique()
                ->constrained((new App())->getTable(), 'id');

            $table->json('state_history_limits')
                ->nullable();

            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_server_products');
    }
};
