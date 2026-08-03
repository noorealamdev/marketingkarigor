<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with('client')->withCount('tasks');
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }
        $projects = $query->latest()->paginate(12);
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,on_hold,completed,cancelled',
            'priority'    => 'required|in:low,medium,high,urgent',
            'start_date'  => 'nullable|date',
            'deadline'    => 'nullable|date|after_or_equal:start_date',
            'client_id'   => 'nullable|exists:clients,id',
        ]);
        Project::create($data);
        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['client', 'tasks.assignees', 'invoices.client']);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $clients = Client::orderBy('name')->get();
        return view('projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,on_hold,completed,cancelled',
            'priority'    => 'required|in:low,medium,high,urgent',
            'start_date'  => 'nullable|date',
            'deadline'    => 'nullable|date',
            'client_id'   => 'nullable|exists:clients,id',
        ]);
        $project->update($data);
        return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }
}
