<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_rubric_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('version')->default(1);
            $table->boolean('is_active')->default(true);
            $table->date('effective_from')->nullable();
            $table->timestamps();
        });

        Schema::create('kpi_rubric_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('kpi_rubric_templates')->cascadeOnDelete();
            $table->string('category')->nullable();
            $table->string('criterion');
            $table->text('description')->nullable();
            $table->decimal('weight', 5, 2)->default(33.33);
            $table->unsignedTinyInteger('max_score')->default(5);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('kpi_periods', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'open', 'closed'])->default('draft');
            $table->decimal('self_weight', 4, 2)->default(0.30);
            $table->decimal('superior_weight', 4, 2)->default(0.70);
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('kpi_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('kpi_rubric_templates')->restrictOnDelete();
            $table->foreignId('period_id')->constrained('kpi_periods')->cascadeOnDelete();
            $table->enum('status', ['pending_self', 'pending_superior', 'finalized'])->default('pending_self');
            $table->enum('pending_role', ['user', 'reviewer', 'admin', 'nobody'])->default('user');
            $table->decimal('self_total', 5, 2)->nullable();
            $table->decimal('superior_total', 5, 2)->nullable();
            $table->decimal('final_total', 5, 2)->nullable();
            $table->timestamp('self_submitted_at')->nullable();
            $table->timestamp('superior_submitted_at')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'period_id']);
        });

        Schema::create('kpi_review_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('kpi_reviews')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('kpi_rubric_items')->restrictOnDelete();
            $table->string('item_label_snapshot');
            $table->decimal('item_weight_snapshot', 5, 2);
            $table->unsignedTinyInteger('self_score')->nullable();
            $table->text('self_comment')->nullable();
            $table->unsignedTinyInteger('superior_score')->nullable();
            $table->text('superior_comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_review_scores');
        Schema::dropIfExists('kpi_reviews');
        Schema::dropIfExists('kpi_periods');
        Schema::dropIfExists('kpi_rubric_items');
        Schema::dropIfExists('kpi_rubric_templates');
    }
};
