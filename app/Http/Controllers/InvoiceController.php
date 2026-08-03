<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['client', 'project']);
        if ($request->search) {
            $query->where('invoice_number', 'like', "%{$request->search}%");
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->client_id) {
            $query->where('client_id', $request->client_id);
        }
        $invoices = $query->latest()->paginate(15);
        $clients  = Client::orderBy('name')->get();
        return view('invoices.index', compact('invoices', 'clients'));
    }

    public function create()
    {
        $clients  = Client::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $next_number = 'INV-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);
        return view('invoices.create', compact('clients', 'projects', 'next_number'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_number' => 'required|string|unique:invoices',
            'client_id'      => 'nullable|exists:clients,id',
            'project_id'     => 'nullable|exists:projects,id',
            'amount'         => 'required|numeric|min:0',
            'status'         => 'required|in:draft,sent,paid,overdue,cancelled',
            'issued_date'    => 'nullable|date',
            'due_date'       => 'nullable|date',
            'notes'          => 'nullable|string',
        ]);
        Invoice::create($data);
        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'project']);
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $clients  = Client::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        return view('invoices.edit', compact('invoice', 'clients', 'projects'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $invoice->id,
            'client_id'      => 'nullable|exists:clients,id',
            'project_id'     => 'nullable|exists:projects,id',
            'amount'         => 'required|numeric|min:0',
            'status'         => 'required|in:draft,sent,paid,overdue,cancelled',
            'issued_date'    => 'nullable|date',
            'due_date'       => 'nullable|date',
            'notes'          => 'nullable|string',
        ]);
        $invoice->update($data);
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated.');
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['client', 'project']);
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'))
            ->setPaper('a4', 'portrait');
        return $pdf->download($invoice->invoice_number . '.pdf');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }
}
