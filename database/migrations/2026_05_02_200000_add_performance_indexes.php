<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('level_id');
            $table->index('unit_id');
            $table->index('superior_id');
            $table->index('is_active');
        });

        Schema::table('kpi_reviews', function (Blueprint $table) {
            $table->index('reviewer_id');
            $table->index('period_id');
            $table->index('status');
        });

        Schema::table('kpi_review_scores', function (Blueprint $table) {
            $table->index('review_id');
        });

        Schema::table('kpi_rubric_templates', function (Blueprint $table) {
            $table->index('level_id');
            $table->index('is_active');
        });

        Schema::table('kpi_periods', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->index('manager_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['level_id']);
            $table->dropIndex(['unit_id']);
            $table->dropIndex(['superior_id']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('kpi_reviews', function (Blueprint $table) {
            $table->dropIndex(['reviewer_id']);
            $table->dropIndex(['period_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('kpi_review_scores', function (Blueprint $table) {
            $table->dropIndex(['review_id']);
        });

        Schema::table('kpi_rubric_templates', function (Blueprint $table) {
            $table->dropIndex(['level_id']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('kpi_periods', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropIndex(['manager_id']);
        });
    }
};
