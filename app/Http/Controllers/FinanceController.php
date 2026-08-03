<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\InvestorPayment;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index()
    {
        $payments = InvestorPayment::with('recordedBy')->latest('received_at')->paginate(15, ['*'], 'payments_page');
        $expenses = Expense::with('recordedBy')->latest('spent_at')->paginate(15, ['*'], 'expenses_page');

        $totalReceived = InvestorPayment::sum('amount');
        $totalSpent    = Expense::sum('amount');
        $netBalance    = $totalReceived - $totalSpent;

        return view('finance.index', compact('payments', 'expenses', 'totalReceived', 'totalSpent', 'netBalance'));
    }

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'amount'      => 'required|numeric|min:0',
            'received_at' => 'required|date',
            'method'      => 'nullable|string|max:100',
            'notes'       => 'nullable|string|max:1000',
        ]);

        InvestorPayment::create($validated + ['recorded_by' => auth()->id()]);

        return redirect()->route('finance.index')->with('success', 'Investor payment recorded.');
    }

    public function destroyPayment(InvestorPayment $payment)
    {
        $payment->delete();
        return redirect()->route('finance.index')->with('success', 'Investor payment deleted.');
    }

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'amount'   => 'required|numeric|min:0',
            'spent_at' => 'required|date',
            'notes'    => 'nullable|string|max:1000',
        ]);

        Expense::create($validated + ['recorded_by' => auth()->id()]);

        return redirect()->route('finance.index')->with('success', 'Expense recorded.');
    }

    public function destroyExpense(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('finance.index')->with('success', 'Expense deleted.');
    }
}
