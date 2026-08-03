<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    private const IN_PROGRESS_STATUSES = ['doing', 'review'];

    public function index()
    {
        $members = User::with('roles')
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'client'))
            ->withCount([
                'assignedTasks',
                'assignedTasks as completed_tasks_count' => fn($q) => $q->where('status', TaskStatus::Done->value),
                'assignedTasks as active_tasks_count'    => fn($q) => $q->where('status', '!=', TaskStatus::Done->value),
            ])
            ->get();

        return view('team.index', compact('members'));
    }

    public function show(User $user)
    {
        $user->load(['roles', 'assignedTasks.project', 'profile']);

        // Build last 6 months stats
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $y    = $date->year;
            $m    = $date->month;

            $base = Task::whereHas('assignees', fn($q) => $q->whereKey($user->id));

            $months[] = [
                'label'       => $date->format('M Y'),
                'year'        => $y,
                'month'       => $m,
                'completed'   => (clone $base)->where('status', TaskStatus::Done->value)
                                    ->whereYear('updated_at', $y)->whereMonth('updated_at', $m)->count(),
                'in_progress' => (clone $base)->whereIn('status', self::IN_PROGRESS_STATUSES)
                                    ->whereYear('updated_at', $y)->whereMonth('updated_at', $m)->count(),
                'total'       => (clone $base)->whereYear('updated_at', $y)->whereMonth('updated_at', $m)->count(),
            ];
        }

        // Current & previous month highlights
        $now  = Carbon::now();
        $prev = Carbon::now()->subMonth();

        $currentMonthDone  = Task::whereHas('assignees', fn($q) => $q->whereKey($user->id))->where('status', TaskStatus::Done->value)
            ->whereYear('updated_at', $now->year)->whereMonth('updated_at', $now->month)->count();
        $previousMonthDone = Task::whereHas('assignees', fn($q) => $q->whereKey($user->id))->where('status', TaskStatus::Done->value)
            ->whereYear('updated_at', $prev->year)->whereMonth('updated_at', $prev->month)->count();

        $activeTasks = Task::whereHas('assignees', fn($q) => $q->whereKey($user->id))
            ->where('status', '!=', TaskStatus::Done->value)
            ->with('project')
            ->orderBy('due_date')
            ->get();

        $roles = Role::all();

        $profile   = $user->profile;
        $documents = $profile ? $profile->getMedia('personal_documents') : collect();

        return view('team.show', compact(
            'user', 'months', 'currentMonthDone', 'previousMonthDone', 'activeTasks', 'roles',
            'profile', 'documents'
        ));
    }

    public function updateRoles(Request $request, User $user)
    {
        $request->validate([
            'role_ids'   => 'array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $superAdminRole = Role::where('name', 'super-admin')->first();
        $keepsSuperAdmin = $superAdminRole && in_array($superAdminRole->id, $request->role_ids ?? []);

        if ($user->isAdmin() && !$keepsSuperAdmin && User::role('super-admin')->count() <= 1) {
            return back()->with('error', 'Cannot remove the Super Admin role from the last Super Admin.');
        }

        $user->roles()->sync($request->role_ids ?? []);
        return redirect()->route('team.show', $user)->with('success', 'Roles updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot remove your own account.');
        }

        if ($user->isAdmin() && User::role('super-admin')->count() <= 1) {
            return back()->with('error', 'Cannot remove the last Super Admin.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('team.index')->with('success', "{$name} has been removed from the team.");
    }

    public function generateResetLink(User $user)
    {
        // Delete any existing token for this user
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        // Create a fresh token
        $token = Str::random(64);
        DB::table('password_reset_tokens')->insert([
            'email'      => $user->email,
            'token'      => bcrypt($token),
            'created_at' => now(),
        ]);

        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false));

        return redirect()
            ->route('team.show', $user)
            ->with('reset_link', $resetUrl)
            ->with('reset_name', $user->name);
    }
}
