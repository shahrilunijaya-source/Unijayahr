<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_suggestions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->boolean('is_anonymous')->default(false);
            $t->string('category', 40);
            $t->string('title', 200);
            $t->text('body');
            $t->enum('status', ['new', 'closed'])->default('new');
            $t->text('management_response')->nullable();
            $t->timestamp('responded_at')->nullable();
            $t->foreignId('responder_id')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->index(['status', 'created_at']);
            $t->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_suggestions');
    }
};
