<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('employee_bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('bank_name', 100)->nullable();
            $table->text('account_number')->nullable(); // encrypted in model
            $table->string('account_holder_name', 200)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('employee_bank_details'); }
};
