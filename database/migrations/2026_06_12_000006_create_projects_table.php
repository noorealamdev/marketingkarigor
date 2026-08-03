<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->string('priority')->default('medium');
            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('deadline');
            $table->index('start_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
