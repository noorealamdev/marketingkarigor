<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['task_id', 'user_id']);
        });

        // Carry over each task's single assignee into the new pivot table.
        $now = now();
        DB::table('tasks')
            ->whereNotNull('assigned_to')
            ->select('id', 'assigned_to')
            ->orderBy('id')
            ->chunk(500, function ($rows) use ($now) {
                DB::table('task_assignees')->insert(
                    $rows->map(fn ($row) => [
                        'task_id'    => $row->id,
                        'user_id'    => $row->assigned_to,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all()
                );
            });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
        });

        // Restore a single assignee per task (first one assigned) so the column isn't left empty.
        DB::table('task_assignees')
            ->select('task_id', DB::raw('MIN(user_id) as user_id'))
            ->groupBy('task_id')
            ->orderBy('task_id')
            ->chunk(500, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('tasks')->where('id', $row->task_id)->update(['assigned_to' => $row->user_id]);
                }
            });

        Schema::dropIfExists('task_assignees');
    }
};
