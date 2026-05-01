<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personality_assessments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('scoring_config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('personality_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('personality_assessments')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('personality_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('personality_sections')->cascadeOnDelete();
            $table->text('prompt');
            $table->enum('type', ['single', 'multi', 'scale', 'text'])->default('single');
            $table->json('options')->nullable();
            $table->json('scoring_weights')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('personality_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained('personality_assessments')->cascadeOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->json('raw_scores')->nullable();
            $table->json('computed_result')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'assessment_id']);
        });

        Schema::create('personality_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('response_id')->constrained('personality_responses')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('personality_questions')->cascadeOnDelete();
            $table->json('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personality_answers');
        Schema::dropIfExists('personality_responses');
        Schema::dropIfExists('personality_questions');
        Schema::dropIfExists('personality_sections');
        Schema::dropIfExists('personality_assessments');
    }
};
