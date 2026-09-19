<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Expense;
use App\Models\Task;
use App\Models\TaskPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskPaymentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        if ($task->status !== TaskStatus::Done->value) {
            return back()->with('error', 'Mark the task as Done before paying for it.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount'  => 'required|numeric|min:0.01',
            'paid_at' => 'required|date',
            'notes'   => 'nullable|string|max:500',
        ]);

        $task->load('assignees');
        $member = $task->assignees->firstWhere('id', (int) $validated['user_id']);
        if (!$member) {
            return back()->with('error', 'You can only pay someone assigned to this task.')->withInput();
        }

        DB::transaction(function () use ($task, $member, $validated) {
            $expense = Expense::create([
                'title'       => "Task payment — {$member->name} ({$task->name})",
                'amount'      => $validated['amount'],
                'spent_at'    => $validated['paid_at'],
                'notes'       => $validated['notes'] ?? null,
                'recorded_by' => auth()->id(),
            ]);

            TaskPayment::create([
                'task_id'    => $task->id,
                'user_id'    => $member->id,
                'expense_id' => $expense->id,
                'amount'     => $validated['amount'],
                'paid_at'    => $validated['paid_at'],
                'notes'      => $validated['notes'] ?? null,
                'paid_by'    => auth()->id(),
            ]);
        });

        return back()->with('success', "Payment recorded for {$member->name}.");
    }

    public function destroy(TaskPayment $payment)
    {
        $payment->delete();

        return back()->with('success', 'Task payment deleted.');
    }
}
