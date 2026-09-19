<?php

use App\Enums\TaskStatus;
use App\Models\Expense;
use App\Models\Task;
use App\Models\TaskPayment;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    foreach (['super-admin', 'project-manager', 'marketer'] as $role) {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }
});

function taskPayUser(string $role): User
{
    $user = User::factory()->create();
    $user->assignRole($role);

    return $user;
}

function doneTaskWith(User $assignee, array $attrs = []): Task
{
    $task = Task::factory()->create(array_merge(['status' => TaskStatus::Done->value], $attrs));
    $task->assignees()->sync([$assignee->id]);

    return $task;
}

test('admin can pay an assignee for a done task and it is logged as an expense', function () {
    $admin      = taskPayUser('super-admin');
    $freelancer = taskPayUser('marketer');
    $task       = doneTaskWith($freelancer, ['payment_amount' => 1500]);

    $this->actingAs($admin)->post(route('tasks.payments.store', $task), [
        'user_id' => $freelancer->id,
        'amount'  => 1500,
        'paid_at' => now()->toDateString(),
        'notes'   => 'bKash',
    ])->assertSessionHas('success');

    $payment = TaskPayment::first();
    expect($payment->user_id)->toBe($freelancer->id)
        ->and((float) $payment->amount)->toBe(1500.0)
        ->and($payment->expense)->not->toBeNull()
        ->and((float) Expense::sum('amount'))->toBe(1500.0);
});

test('deleting a task payment removes its finance expense', function () {
    $admin      = taskPayUser('super-admin');
    $freelancer = taskPayUser('marketer');
    $task       = doneTaskWith($freelancer);

    $this->actingAs($admin)->post(route('tasks.payments.store', $task), [
        'user_id' => $freelancer->id, 'amount' => 500, 'paid_at' => now()->toDateString(),
    ]);

    $this->actingAs($admin)->delete(route('tasks.payments.destroy', TaskPayment::first()));

    expect(TaskPayment::count())->toBe(0)->and(Expense::count())->toBe(0);
});

test('cannot pay for a task that is not done', function () {
    $admin      = taskPayUser('super-admin');
    $freelancer = taskPayUser('marketer');
    $task       = doneTaskWith($freelancer, ['status' => TaskStatus::Doing->value]);

    $this->actingAs($admin)->post(route('tasks.payments.store', $task), [
        'user_id' => $freelancer->id, 'amount' => 500, 'paid_at' => now()->toDateString(),
    ])->assertSessionHas('error');

    expect(TaskPayment::count())->toBe(0);
});

test('cannot pay someone who is not assigned to the task', function () {
    $admin    = taskPayUser('super-admin');
    $assignee = taskPayUser('marketer');
    $outsider = taskPayUser('marketer');
    $task     = doneTaskWith($assignee);

    $this->actingAs($admin)->post(route('tasks.payments.store', $task), [
        'user_id' => $outsider->id, 'amount' => 500, 'paid_at' => now()->toDateString(),
    ])->assertSessionHas('error');

    expect(TaskPayment::count())->toBe(0);
});

test('project managers and members cannot record task payments', function () {
    $freelancer = taskPayUser('marketer');
    $task       = doneTaskWith($freelancer);
    $payload    = ['user_id' => $freelancer->id, 'amount' => 500, 'paid_at' => now()->toDateString()];

    $this->actingAs(taskPayUser('project-manager'))->post(route('tasks.payments.store', $task), $payload)->assertForbidden();
    $this->actingAs($freelancer)->post(route('tasks.payments.store', $task), $payload)->assertForbidden();

    expect(TaskPayment::count())->toBe(0);
});

test('only the admin can set the task fee, and status updates do not wipe it', function () {
    $admin = taskPayUser('super-admin');
    $pm    = taskPayUser('project-manager');
    $task  = Task::factory()->create(['payment_amount' => 800, 'status' => TaskStatus::Doing->value]);

    $base = ['name' => $task->name, 'status' => TaskStatus::Done->value, 'priority' => 'medium'];

    // PM tries to change the fee: ignored.
    $this->actingAs($pm)->patch(route('tasks.update', $task), $base + ['payment_amount' => 1])->assertRedirect();
    expect((float) $task->fresh()->payment_amount)->toBe(800.0);

    // Admin quick-status update (no payment_amount field): fee preserved.
    $this->actingAs($admin)->patch(route('tasks.update', $task->fresh()), $base);
    expect((float) $task->fresh()->payment_amount)->toBe(800.0);

    // Admin sets the fee explicitly.
    $this->actingAs($admin)->patch(route('tasks.update', $task->fresh()), $base + ['payment_amount' => 2000]);
    expect((float) $task->fresh()->payment_amount)->toBe(2000.0);
});
