<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    private const UPLOAD_MIMES = 'jpg,jpeg,png,gif,webp,mp4,mov,webm,avi,mkv,'
        . 'pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,'
        . 'psd,ai,eps,indd,obj,fbx,blend,stl,mp3,wav,ogg,zip,rar,ttf,otf,woff,woff2';

    private array $collections = [
        'images'    => ['label' => 'Images',     'mimes' => ['image/']],
        'videos'    => ['label' => 'Videos',     'mimes' => ['video/']],
        'documents' => ['label' => 'Documents',  'mimes' => ['application/pdf', 'application/msword', 'application/vnd', 'text/']],
        'renders'   => ['label' => '3D / Renders', 'mimes' => []],
        'other'     => ['label' => 'Other',      'mimes' => []],
    ];

    public function index(Request $request, Project $project)
    {
        $filter   = $request->query('type', 'all');
        $isAdmin  = auth()->user()->isAdmin();
        $userId   = auth()->id();

        $allMediaRaw = $project->getMedia('*');

        if (!$isAdmin) {
            $allMediaRaw = $allMediaRaw->filter(
                fn($m) => ($m->getCustomProperty('uploaded_by') == $userId)
            );
        }

        $counts = [];
        foreach (array_keys($this->collections) as $col) {
            $col_media = $project->getMedia($col);
            if (!$isAdmin) {
                $col_media = $col_media->filter(
                    fn($m) => ($m->getCustomProperty('uploaded_by') == $userId)
                );
            }
            $counts[$col] = $col_media->count();
        }

        $filtered = $filter === 'all'
            ? $allMediaRaw
            : ($isAdmin
                ? $project->getMedia($filter)
                : $project->getMedia($filter)->filter(
                    fn($m) => ($m->getCustomProperty('uploaded_by') == $userId)
                ));

        $totalSize = $allMediaRaw->sum('size');
        $allMedia  = $allMediaRaw;

        return view('projects.media', compact(
            'project', 'filtered', 'allMedia', 'counts', 'filter', 'totalSize'
        ));
    }

    public function store(Request $request, Project $project)
    {
        // Override at runtime — artisan serve uses CLI php.ini which may differ from web SAPI
        @ini_set('upload_max_filesize', '512M');
        @ini_set('post_max_size', '512M');
        @ini_set('max_execution_time', '300');
        @ini_set('max_input_time', '300');

        $request->validate([
            'file'        => 'required|file|max:102400|mimes:' . self::UPLOAD_MIMES,
            'collection'  => 'nullable|string|in:images,videos,documents,renders,other',
            'custom_name' => 'nullable|string|max:255',
        ]);

        $file       = $request->file('file');
        $mime       = $file->getMimeType();
        $collection = $request->input('collection') ?: $this->detectCollection($mime);

        $media = $project->addMediaFromRequest('file')
            ->usingName($request->input('custom_name') ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            ->usingFileName($file->getClientOriginalName())
            ->withCustomProperties(['uploaded_by' => auth()->id()])
            ->toMediaCollection($collection);

        $this->cleanTempDirectory();

        if ($request->wantsJson()) {
            return response()->json(['id' => $media->id, 'url' => rebase_media_url($media->getUrl())]);
        }

        return redirect()->route('projects.media', [$project, 'type' => $collection])
            ->with('success', 'File uploaded successfully.');
    }

    public function download(Project $project, Media $media)
    {
        abort_unless($media->model_id === $project->id, 403);

        $path = $media->getPath();

        if (!file_exists($path)) {
            abort(404, 'File not found on disk.');
        }

        return response()->download($path, $media->file_name, [
            'Content-Type' => $media->mime_type,
        ]);
    }

    public function destroy(Project $project, Media $media)
    {
        abort_unless($media->model_id === $project->id, 403);
        $collection = $media->collection_name;
        $media->delete();

        return redirect()->route('projects.media', [$project, 'type' => $collection])
            ->with('success', 'File deleted.');
    }

    private function cleanTempDirectory(): void
    {
        $tempPath = storage_path('media-library/temp');
        if (is_dir($tempPath)) {
            File::deleteDirectory($tempPath);
        }
    }

    private function detectCollection(string $mime): string
    {
        if (str_starts_with($mime, 'image/')) return 'images';
        if (str_starts_with($mime, 'video/')) return 'videos';
        if (in_array($mime, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain', 'text/csv',
        ])) return 'documents';
        return 'other';
    }
}
