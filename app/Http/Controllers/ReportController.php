<?php

namespace App\Http\Controllers;

use App\Enums\ReportPeriod;
use App\Models\Client;
use App\Models\PerformanceReport;
use App\Models\Project;
use App\Notifications\ReportShared;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = PerformanceReport::with(['client', 'project']);
        if ($request->client_id) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->period_type) {
            $query->where('period_type', $request->period_type);
        }
        $reports = $query->latest('period_start')->paginate(15);
        $clients = Client::orderBy('name')->get();
        return view('reports.index', compact('reports', 'clients'));
    }

    public function create(Request $request)
    {
        $clients  = Client::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $selected_client = $request->client_id ? Client::find($request->client_id) : null;
        return view('reports.create', compact('clients', 'projects', 'selected_client'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $report = PerformanceReport::create($data + ['created_by' => auth()->id()]);

        return redirect()->route('reports.show', $report)->with('success', 'Report created successfully.');
    }

    public function show(PerformanceReport $report)
    {
        $report->load(['client', 'project', 'createdBy']);
        return view('reports.show', compact('report'));
    }

    public function edit(PerformanceReport $report)
    {
        $clients  = Client::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        return view('reports.edit', compact('report', 'clients', 'projects'));
    }

    public function update(Request $request, PerformanceReport $report)
    {
        $report->update($this->validated($request, $report));
        return redirect()->route('reports.show', $report)->with('success', 'Report updated.');
    }

    public function send(PerformanceReport $report)
    {
        $report->load('client.users');
        $recipients = $report->client->users;

        if ($recipients->isEmpty()) {
            return back()->with('error', 'This client has no portal login yet — invite a client contact first.');
        }

        foreach ($recipients as $recipient) {
            try {
                $recipient->notify(new ReportShared($report, auth()->user()));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $report->update(['sent_at' => now()]);

        return back()->with('success', 'Report sent to ' . $recipients->pluck('name')->implode(', ') . '.');
    }

    public function pdf(PerformanceReport $report)
    {
        $report->load(['client', 'project']);
        $pdf = Pdf::loadView('reports.pdf', compact('report'))->setPaper('a4', 'portrait');
        $filename = $report->periodEnum()->value . '-report-' . $report->period_start->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    public function destroy(PerformanceReport $report)
    {
        $report->delete();
        return redirect()->route('reports.index')->with('success', 'Report deleted.');
    }

    public function clientIndex()
    {
        $reports = PerformanceReport::where('client_id', auth()->user()->client_id)
            ->whereNotNull('sent_at')
            ->latest('period_start')
            ->paginate(10);

        return view('client.reports.index', compact('reports'));
    }

    public function clientShow(PerformanceReport $report)
    {
        abort_unless(
            $report->client_id === auth()->user()->client_id && $report->isSent(),
            404
        );

        return view('client.reports.show', compact('report'));
    }

    private function validated(Request $request, ?PerformanceReport $report = null): array
    {
        $data = $request->validate([
            'client_id'             => 'required|exists:clients,id',
            'project_id'            => 'nullable|exists:projects,id',
            'period_type'           => ['required', new Enum(ReportPeriod::class)],
            'period_start'          => 'required|date',
            'period_end'            => 'required|date|after_or_equal:period_start',
            'reach'                 => 'nullable|integer|min:0',
            'engagement'            => 'nullable|integer|min:0',
            'video_views'           => 'nullable|integer|min:0',
            'best_performing_post'  => 'nullable|string|max:255',
            'next_plan'             => 'nullable|string',
            'notes'                 => 'nullable|string',
        ]);

        return $data;
    }
}
