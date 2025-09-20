<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('certificate_templates', function (Blueprint $table) {
            $table->string('primary_color')->nullable()->after('is_default');
            $table->string('watermark')->nullable()->after('primary_color');
            $table->string('course_line_prefix')->nullable()->after('watermark');
            $table->string('logo')->nullable()->after('course_line_prefix');
            $table->string('signature')->nullable()->after('logo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificate_templates', function (Blueprint $table) {
             $table->dropColumn([
                'primary_color',
                'watermark',
                'course_line_prefix',
                'logo',
                'signature'
            ]);
        });
    }
};
