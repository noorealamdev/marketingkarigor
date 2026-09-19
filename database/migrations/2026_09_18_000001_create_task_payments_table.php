<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agreed fee for a task (what the assignee/freelancer earns when it's done).
        Schema::table('tasks', function (Blueprint $table) {
            $table->decimal('payment_amount', 12, 2)->nullable()->after('due_date');
        });

        Schema::create('task_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('paid_at');
            $table->text('notes')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_payments');

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('payment_amount');
        });
    }
};
