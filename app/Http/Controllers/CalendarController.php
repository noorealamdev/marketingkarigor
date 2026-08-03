<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar.index');
    }

    public function events(Request $request)
    {
        $events = [];

        // FullCalendar sends the visible grid's date range on every fetch (initial
        // load, prev/next, view switch) — scope every query to it instead of
        // loading every project/task ever created. Fall back to the current month
        // if the params are ever missing (defensive, shouldn't happen in practice).
        $start = $request->query('start') ?? now()->startOfMonth()->toDateString();
        $end   = $request->query('end') ?? now()->endOfMonth()->toDateString();

        // Project deadlines
        $projects = Project::whereNotNull('deadline')->whereBetween('deadline', [$start, $end])->get();
        foreach ($projects as $p) {
            $events[] = [
                'id'              => 'project-' . $p->id,
                'title'           => '📁 ' . $p->name,
                'start'           => $p->deadline->format('Y-m-d'),
                'url'             => route('projects.show', $p),
                'backgroundColor' => match($p->status) {
                    'active'   => '#6c63ff',
                    'on_hold'  => '#f59e0b',
                    'done'     => '#4ade80',
                    default    => '#4a5068',
                },
                'borderColor'     => 'transparent',
                'classNames'      => ['fc-project-event'],
            ];
        }

        // Project start dates
        $projects2 = Project::whereNotNull('start_date')->whereBetween('start_date', [$start, $end])->get();
        foreach ($projects2 as $p) {
            $events[] = [
                'id'              => 'project-start-' . $p->id,
                'title'           => '▶ ' . $p->name,
                'start'           => $p->start_date->format('Y-m-d'),
                'backgroundColor' => 'rgba(108,99,255,0.25)',
                'borderColor'     => '#6c63ff',
                'textColor'       => '#a89fff',
            ];
        }

        // Task due dates
        $tasks = Task::whereNotNull('due_date')->whereBetween('due_date', [$start, $end])->with('assignees')->get();
        foreach ($tasks as $t) {
            $color = match($t->priority) {
                'high'   => '#f87171',
                'medium' => '#fbbf24',
                default  => '#60a5fa',
            };
            $events[] = [
                'id'              => 'task-' . $t->id,
                'title'           => '✓ ' . $t->name,
                'start'           => $t->due_date->format('Y-m-d'),
                'url'             => route('tasks.show', $t),
                'backgroundColor' => $t->status === TaskStatus::Done->value ? 'rgba(74,222,128,0.2)' : $color,
                'borderColor'     => $t->status === TaskStatus::Done->value ? '#4ade80' : $color,
                'textColor'       => $t->status === TaskStatus::Done->value ? '#4ade80' : '#fff',
                'extendedProps'   => [
                    'status'   => $t->status,
                    'priority' => $t->priority,
                    'assignee' => $t->assignees->isNotEmpty() ? $t->assignees->pluck('name')->join(', ') : 'Unassigned',
                ],
            ];
        }

        return response()->json($events);
    }
}
