<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('period_type')->default('weekly'); // daily | weekly | monthly
            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedInteger('reach')->nullable();
            $table->unsignedInteger('engagement')->nullable();
            $table->unsignedInteger('video_views')->nullable();
            $table->string('best_performing_post')->nullable();
            $table->text('next_plan')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['client_id', 'period_type', 'period_start']);
            $table->index('sent_at');
            $table->index('period_start');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_reports');
    }
};
