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
            $table->string('report_type')->nullable(); // Facebook Marketing | Google Ads | TikTok Ads | Web Development | …
            $table->string('title')->nullable();
            $table->string('period_type')->default('monthly'); // one-time | weekly | monthly
            $table->date('period_start');
            $table->date('period_end');
            $table->json('metrics')->nullable(); // flexible [{label, value}] list — service-agnostic
            $table->text('summary')->nullable();
            $table->text('next_plan')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index('sent_at');
            $table->index('period_start');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_reports');
    }
};
