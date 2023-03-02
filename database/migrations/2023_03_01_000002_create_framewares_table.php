<?php

use dnj\AAA\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('iot_server_framewares', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'owner_id');
            $table->string('name', 100)->collation('latin1_general_ci')->unique();
            $table->string('file', 255);
            $table->unsignedInteger('version');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_server_framewares');
    }
};
