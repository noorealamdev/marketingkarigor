<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\SalaryPayment;
use App\Models\SalaryRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index()
    {
        $members = User::with(['roles', 'currentSalary'])
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'client'))
            ->orderBy('name')->get();

        $totalMonthly = $members->sum(function ($m) {
            $s = $m->currentSalary;
            if (!$s) return 0;
            return match ($s->period) {
                'yearly'  => $s->amount / 12,
                'weekly'  => $s->amount * 4.33,
                default   => $s->amount,
            };
        });

        $currentPeriod = now()->startOfMonth();
        $paidUserIds = SalaryPayment::whereDate('period_month', $currentPeriod)->pluck('user_id')->all();

        return view('salaries.index', compact('members', 'totalMonthly', 'paidUserIds'));
    }

    public function show(User $user)
    {
        $records = $user->salaryRecords()->with('createdBy')->latest('effective_date')->get();
        $current = $records->first();
        $payments = $user->salaryPayments()->with(['paidBy', 'expense'])->get();

        return view('salaries.show', compact('user', 'records', 'current', 'payments'));
    }

    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'amount'         => 'required|numeric|min:0',
            'period'         => 'required|in:monthly,weekly,yearly',
            'effective_date' => 'required|date',
            'notes'          => 'nullable|string|max:500',
        ]);

        $user->salaryRecords()->create([
            'amount'         => $validated['amount'],
            'currency'       => 'BDT',
            'period'         => $validated['period'],
            'effective_date' => $validated['effective_date'],
            'notes'          => $validated['notes'] ?? null,
            'created_by'     => auth()->id(),
        ]);

        return redirect()->route('salaries.show', $user)
            ->with('success', "Salary updated for {$user->name}.");
    }

    public function destroy(SalaryRecord $record)
    {
        $user = $record->user;
        $record->delete();
        return redirect()->route('salaries.show', $user)
            ->with('success', 'Salary record deleted.');
    }

    public function storePayment(Request $request, User $user)
    {
        $validated = $request->validate([
            'amount'       => 'required|numeric|min:0',
            'period_month' => 'required|date',
            'paid_at'      => 'required|date',
            'notes'        => 'nullable|string|max:500',
        ]);

        $periodMonth = Carbon::parse($validated['period_month'])->startOfMonth();
        $periodLabel = $periodMonth->format('F Y');

        $expense = Expense::create([
            'title'       => "Salary — {$user->name} ({$periodLabel})",
            'amount'      => $validated['amount'],
            'spent_at'    => $validated['paid_at'],
            'notes'       => $validated['notes'] ?? null,
            'recorded_by' => auth()->id(),
        ]);

        $user->salaryPayments()->create([
            'salary_record_id' => $user->currentSalary?->id,
            'expense_id'        => $expense->id,
            'amount'            => $validated['amount'],
            'period_month'      => $periodMonth,
            'paid_at'           => $validated['paid_at'],
            'notes'             => $validated['notes'] ?? null,
            'paid_by'           => auth()->id(),
        ]);

        return redirect()->route('salaries.show', $user)
            ->with('success', "Payment recorded for {$user->name} ({$periodLabel}).");
    }

    public function destroyPayment(SalaryPayment $payment)
    {
        $user = $payment->user;
        $payment->delete();

        return redirect()->route('salaries.show', $user)
            ->with('success', 'Payment record deleted.');
    }
}
