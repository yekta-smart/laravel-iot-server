<?php

use dnj\AAA\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('iot_server_folders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owner_id')
                ->constrained((new User())->getTable(), 'id');

            $table->foreignId('parent_id')
                ->nullable()
                ->references('id')
                ->on('iot_server_folders')
                ->cascadeOnDelete();

            $table->string('title');

            $table->timestamp('created_at');

            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_server_folders');
    }
};
