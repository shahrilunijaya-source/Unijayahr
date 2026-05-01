<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->char('code', 2)->unique();
            $table->unsignedTinyInteger('order');
            $table->string('name');
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_mid', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('head_user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->unsignedSmallInteger('order')->default(0);
            $table->unsignedBigInteger('head_user_id')->nullable();
            $table->timestamps();

            $table->unique(['department_id', 'name']);
        });

        // Add FK constraints to users now that levels/units exist
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('level_id')->references('id')->on('levels')->nullOnDelete();
            $table->foreign('unit_id')->references('id')->on('units')->nullOnDelete();
            $table->foreign('superior_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['level_id']);
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['superior_id']);
        });

        Schema::dropIfExists('units');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('levels');
    }
};
