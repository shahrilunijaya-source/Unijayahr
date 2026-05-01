<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('ic_number')->nullable()->after('email');
            $table->string('phone', 20)->nullable()->after('ic_number');
            $table->unsignedBigInteger('level_id')->nullable()->after('phone');
            $table->unsignedBigInteger('unit_id')->nullable()->after('level_id');
            $table->unsignedBigInteger('superior_id')->nullable()->after('unit_id');
            $table->date('join_date')->nullable()->after('superior_id');
            $table->enum('status', ['active', 'inactive', 'on_leave'])->default('active')->after('join_date');
            $table->string('avatar_path')->nullable()->after('status');
            $table->string('google_id')->nullable()->unique()->after('avatar_path');
            $table->string('provider', 20)->nullable()->after('google_id');
            $table->boolean('is_active')->default(true)->after('provider');
            $table->boolean('must_change_password')->default(false)->after('is_active');
            $table->timestamp('last_login_at')->nullable()->after('must_change_password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'ic_number', 'phone', 'level_id', 'unit_id', 'superior_id',
                'join_date', 'status', 'avatar_path', 'google_id', 'provider',
                'is_active', 'must_change_password', 'last_login_at',
            ]);
        });
    }
};
