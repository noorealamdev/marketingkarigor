<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectAssetController extends Controller
{
    public function index(Request $request, Project $project)
    {
        $assets = $project->assets()->with('uploader')->latest()->get();
        $selectedCat = $request->query('category', 'all');
        return view('projects.assets', compact('project', 'assets', 'selectedCat'));
    }

    public function store(Request $request, Project $project)
    {
        $request->validate([
            'file'        => 'required|file|max:51200',
            'name'        => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:50',
        ]);

        $file     = $request->file('file');
        $origName = $file->getClientOriginalName();
        $path     = $file->store("project_assets/{$project->id}", 'public');

        ProjectAsset::create([
            'project_id'  => $project->id,
            'name'        => $request->name ?: pathinfo($origName, PATHINFO_FILENAME),
            'description' => $request->description,
            'file_path'   => $path,
            'file_name'   => $origName,
            'file_type'   => $file->getMimeType(),
            'file_size'   => $file->getSize(),
            'category'    => $request->category ?? 'general',
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->route('projects.assets', $project)->with('success', 'Asset uploaded successfully.');
    }

    public function destroy(Project $project, ProjectAsset $asset)
    {
        abort_unless($asset->project_id === $project->id, 404);
        $asset->delete();
        return redirect()->route('projects.assets', $project)->with('success', 'Asset deleted.');
    }
}
