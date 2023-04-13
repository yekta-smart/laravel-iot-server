<?php

use dnj\AAA\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use YektaSmart\IotServer\Models\Folder;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('iot_server_folders_users', function (Blueprint $table) {
            $table->foreignId('folder_id')
                ->constrained((new Folder())->getTable(), 'id')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained((new User())->getTable(), 'id')
                ->cascadeOnDelete();

            $table->primary(['folder_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_server_folders_users');
    }
};
