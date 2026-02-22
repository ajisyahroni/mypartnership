<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable(); // ID user (jika ada)
            $table->string('url'); // URL yang diakses
            $table->string('method'); // HTTP method (GET, POST, dll)
            $table->text('request_data')->nullable(); // Data yang dikirim
            $table->text('response_data')->nullable(); // Data respons
            $table->string('ip_address'); // IP pengguna
            $table->string('status_code'); // Status HTTP (200, 500, dll)
            $table->text('error_message')->nullable(); // Pesan error jika ada
            $table->timestamps(); // Waktu aktivitas/error terjadi
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};
