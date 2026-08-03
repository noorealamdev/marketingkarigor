<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TaskAttachmentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $this->authorizeTaskAccess($task);

        $request->validate([
            'files'   => 'required|array|min:1',
            'files.*' => 'file|max:512000',
        ]);

        foreach ($request->file('files') as $file) {
            $task->addMedia($file)
                ->withCustomProperties(['uploaded_by' => auth()->id()])
                ->toMediaCollection('attachments');
        }

        $this->cleanTempDirectory();

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', count($request->file('files')) . ' file(s) attached.');
    }

    private function cleanTempDirectory(): void
    {
        $tempPath = storage_path('media-library/temp');
        if (is_dir($tempPath)) {
            File::deleteDirectory($tempPath);
        }
    }

    public function download(Task $task, Media $media)
    {
        $this->authorizeTaskAccess($task);
        abort_unless(
            $media->model_id === $task->id && $media->model_type === Task::class,
            403
        );

        $path = $media->getPath();

        if (!file_exists($path)) {
            abort(404, 'File not found on disk.');
        }

        return response()->download($path, $media->file_name, [
            'Content-Type' => $media->mime_type,
        ]);
    }

    public function destroy(Task $task, Media $media)
    {
        $this->authorizeTaskAccess($task);
        abort_unless(
            $media->model_id === $task->id && $media->model_type === Task::class,
            403
        );
        abort_unless(
            $media->getCustomProperty('uploaded_by') == auth()->id() || auth()->user()->isAdmin(),
            403,
            'You can only delete your own attachments.'
        );

        $media->delete();

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Attachment deleted.');
    }

    private function authorizeTaskAccess(Task $task): void
    {
        $user = auth()->user();
        abort_unless(
            $user->hasAnyRole(['super-admin', 'project-manager']) || $task->isAssignedTo($user),
            403
        );
    }
}
