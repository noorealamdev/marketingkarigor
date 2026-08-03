<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use App\Notifications\ContentApproved;
use App\Notifications\RevisionRequested;
use Illuminate\Http\Request;

class ContentApprovalController extends Controller
{
    private const UPLOAD_MIMES = 'jpg,jpeg,png,gif,webp,mp4,mov,webm,avi,mkv,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv';

    public function index()
    {
        $tasks = Task::whereHas('project', fn ($q) => $q->where('client_id', auth()->user()->client_id))
            ->whereNotNull('shared_with_client_at')
            ->with('project')
            ->latest('shared_with_client_at')
            ->paginate(10);

        return view('client.content.index', compact('tasks'));
    }

    public function show(Task $task)
    {
        abort_unless(
            $task->project?->client_id === auth()->user()->client_id && $task->shared_with_client_at,
            404
        );

        $task->load(['project', 'visibleComments.author', 'visibleComments.media']);

        return view('client.content.show', compact('task'));
    }

    public function approve(Task $task)
    {
        abort_unless(
            $task->project?->client_id === auth()->user()->client_id && $task->shared_with_client_at,
            404
        );

        $approver = auth()->user();
        $task->update([
            'client_approved_at' => now(),
            'status'             => TaskStatus::Done->value,
        ]);

        $this->notifyStaff($task, fn (User $u) => $u->notify(new ContentApproved($task, $approver)));

        return redirect()->route('client.content.show', $task)->with('success', 'Content approved. Thank you!');
    }

    public function requestRevision(Request $request, Task $task)
    {
        abort_unless(
            $task->project?->client_id === auth()->user()->client_id && $task->shared_with_client_at,
            404
        );

        $request->validate([
            'body'       => 'nullable|string|max:5000',
            'files'      => 'nullable|array|max:10',
            'files.*'    => 'file|max:102400|mimes:' . self::UPLOAD_MIMES,
        ]);

        $body  = trim($request->input('body', ''));
        $files = $request->file('files') ?? [];

        if ($body === '' && empty($files)) {
            return back()->withErrors(['body' => 'Please describe the changes you\'d like, or attach a reference file.'])->withInput();
        }

        $requester = auth()->user();

        $comment = TaskComment::create([
            'task_id'           => $task->id,
            'user_id'           => $requester->id,
            'body'              => $body,
            'visible_to_client' => true,
        ]);

        foreach ($files as $file) {
            $comment->addMedia($file)
                ->usingName($file->getClientOriginalName())
                ->usingFileName($file->getClientOriginalName())
                ->toMediaCollection('comment_files');
        }

        $task->update([
            'client_approved_at' => null,
            'status'             => TaskStatus::Doing->value,
        ]);

        $this->notifyStaff($task, fn (User $u) => $u->notify(new RevisionRequested($task, $comment, $requester)));

        return redirect()->route('client.content.show', $task)->with('success', 'Revision request sent to the team.');
    }

    private function notifyStaff(Task $task, \Closure $notify): void
    {
        $task->load('assignees');
        $recipients = $task->assignees;

        $managers = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['super-admin', 'project-manager']))
            ->whereNotIn('id', $recipients->pluck('id'))
            ->get();

        foreach ($recipients->merge($managers) as $user) {
            try {
                $notify($user);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
}
