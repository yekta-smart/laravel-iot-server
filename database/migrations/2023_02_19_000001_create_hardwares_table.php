<?php

use dnj\AAA\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('iot_server_hardwares', function (Blueprint $table) {
            $table->id();

            $table->string('serial', 32)
                ->collation('latin1_general_ci')
                ->unique();

            $table->foreignId('owner_id')
                ->constrained((new User())->getTable(), 'id');

            $table->string('name', 100)
                ->collation('latin1_general_ci')
                ->unique();

            $table->unsignedInteger('version');

            $table->timestamp('created_at');

            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_server_hardwares');
    }
};
