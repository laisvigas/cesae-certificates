<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained()->cascadeOnDelete();

            $table->string('ref')->unique(); // código de verificação/autenticidade
            $table->timestamp('issued_at');

            $table->timestamps();

            $table->unique(['event_id','participant_id']); // 1 cert por participante+evento
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
