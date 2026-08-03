<?php

namespace App\Http\Controllers;

use App\Models\CommentReaction;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;

class CommentReactionController extends Controller
{
    public function toggle(Request $request, Task $task, TaskComment $comment): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        abort_unless(
            $user->hasAnyRole(['super-admin', 'project-manager']) || $task->isAssignedTo($user),
            403
        );
        abort_unless($comment->task_id === $task->id, 404);

        $emoji  = $request->validate(['emoji' => 'required|string|max:20'])['emoji'];
        $userId = auth()->id();

        $existing = CommentReaction::where([
            'comment_id' => $comment->id,
            'user_id'    => $userId,
            'emoji'      => $emoji,
        ])->first();

        if ($existing) {
            $existing->delete();
            $reacted = false;
        } else {
            CommentReaction::create([
                'comment_id' => $comment->id,
                'user_id'    => $userId,
                'emoji'      => $emoji,
            ]);
            $reacted = true;
        }

        $count = CommentReaction::where([
            'comment_id' => $comment->id,
            'emoji'      => $emoji,
        ])->count();

        return response()->json(['reacted' => $reacted, 'count' => $count]);
    }
}
