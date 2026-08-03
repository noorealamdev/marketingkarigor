<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use App\Notifications\TaskCommented;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
    private const UPLOAD_MIMES = 'jpg,jpeg,png,gif,webp,mp4,mov,webm,avi,mkv,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv';

    public function store(Request $request, Task $task)
    {
        $user = auth()->user();
        abort_unless(
            $user->hasAnyRole(['super-admin', 'project-manager']) || $task->isAssignedTo($user),
            403
        );

        // Defensive — the real fix is upload_max_filesize/post_max_size in php.ini,
        // but max_execution_time/max_input_time can still be raised at runtime.
        @ini_set('max_execution_time', '300');
        @ini_set('max_input_time', '300');

        $request->validate([
            'body'            => 'nullable|string|max:5000',
            'comment_files'   => 'nullable|array|max:10',
            'comment_files.*' => 'file|max:102400|mimes:' . self::UPLOAD_MIMES,
        ]);

        $body  = trim($request->input('body', ''));
        $files = $request->file('comment_files') ?? [];

        if ($body === '' && empty($files)) {
            return back()
                ->withErrors(['body' => 'Please write a comment or attach at least one file.'])
                ->withInput();
        }

        $comment = TaskComment::create([
            'task_id'           => $task->id,
            'user_id'           => auth()->id(),
            'body'              => $body,
            'visible_to_client' => $request->boolean('visible_to_client'),
        ]);

        foreach ($files as $file) {
            $comment->addMedia($file)
                ->usingName($file->getClientOriginalName())
                ->usingFileName($file->getClientOriginalName())
                ->toMediaCollection('comment_files');
        }

        $task->load('assignees');
        $commenter = auth()->user();

        $toNotify = $task->assignees->where('id', '!=', $commenter->id);

        if ($task->project) {
            $admins = User::whereHas('roles', fn($q) => $q->whereIn('name', ['super-admin', 'project-manager']))
                ->where('id', '!=', $commenter->id)
                ->whereNotIn('id', $toNotify->pluck('id'))
                ->get();
            $toNotify = $toNotify->merge($admins);
        }

        foreach ($toNotify as $user) {
            try {
                $user->notify(new TaskCommented($task, $comment, $commenter));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->route('tasks.show', $task)->with('success', 'Comment posted.');
    }

    public function destroy(Task $task, TaskComment $comment)
    {
        abort_unless(
            $comment->user_id === auth()->id(),
            403,
            'You can only delete your own comments.'
        );
        $comment->delete();
        return redirect()->route('tasks.show', $task)->with('success', 'Comment deleted.');
    }
}
