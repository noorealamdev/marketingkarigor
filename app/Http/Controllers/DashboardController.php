<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\Invoice;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_clients'   => Client::count(),
            'active_projects' => Project::where('status', 'active')->count(),
            'pending_tasks'   => Task::whereNotIn('status', [TaskStatus::Done->value])->count(),
            'total_revenue'   => Invoice::where('status', 'paid')->sum('amount'),
        ];

        $recent_projects = Project::with('client')->latest()->take(5)->get();
        $recent_tasks    = Task::with('project')->latest()->take(5)->get();
        $upcoming_tasks  = Task::whereNotNull('due_date')
            ->whereNotIn('status', [TaskStatus::Done->value])
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $project_statuses = Project::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('dashboard', compact(
            'stats', 'recent_projects', 'recent_tasks', 'upcoming_tasks', 'project_statuses'
        ));
    }
}
