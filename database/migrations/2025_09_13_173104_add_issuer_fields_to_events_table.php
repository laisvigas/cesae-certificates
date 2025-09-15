<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('issuer_institution', 150)->nullable()->after('hours');
            $table->string('issuer_name', 120)->nullable()->after('issuer_institution');
            $table->string('issuer_role', 120)->nullable()->after('issuer_name');
            $table->string('issuer_signature_path', 255)->nullable()->after('issuer_role');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'issuer_institution',
                'issuer_name',
                'issuer_role',
                'issuer_signature_path',
            ]);
        });
    }
};
