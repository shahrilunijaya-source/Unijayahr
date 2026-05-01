<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kpi_review_scores', function (Blueprint $table) {
            $table->unsignedTinyInteger('max_score_snapshot')->default(5)->after('item_weight_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('kpi_review_scores', function (Blueprint $table) {
            $table->dropColumn('max_score_snapshot');
        });
    }
};
