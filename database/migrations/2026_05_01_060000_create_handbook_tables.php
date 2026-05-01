<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('handbook_parts', function (Blueprint $table) {
            $table->id();
            $table->char('code', 2)->unique();
            $table->string('title');
            $table->string('tagline')->nullable();
            $table->text('intro_body')->nullable();
            $table->char('accent_color', 7)->default('#533afd');
            $table->string('icon', 60)->default('heroicon-o-document-text');
            $table->unsignedSmallInteger('order');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('content_sections', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['handbook', 'sop', 'law_act', 'onboarding'])->default('handbook');
            $table->string('title');
            $table->string('slug');
            $table->longText('body');
            $table->unsignedSmallInteger('order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->foreignId('last_updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('part_id')->nullable()->constrained('handbook_parts')->nullOnDelete();
            $table->timestamps();

            $table->unique(['category', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_sections');
        Schema::dropIfExists('handbook_parts');
    }
};
