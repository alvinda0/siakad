<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 20);           // created | updated | deleted | login | logout
            $table->string('model_type')->nullable(); // App\Models\User, dsb.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('model_label')->nullable(); // nama/email biar mudah dibaca
            $table->json('old_data')->nullable();    // data sebelum (update/delete)
            $table->json('new_data')->nullable();    // data sesudah (create/update)
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
