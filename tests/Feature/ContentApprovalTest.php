<?php

use App\Enums\TaskStatus;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\ContentReadyForApproval;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'project-manager', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);
});

function sharedTaskFor(Client $client, array $taskAttributes = []): Task
{
    $project = Project::factory()->create(['client_id' => $client->id]);

    return Task::factory()->create(array_merge([
        'project_id'             => $project->id,
        'status'                 => TaskStatus::Review->value,
        'shared_with_client_at'  => now(),
    ], $taskAttributes));
}

function clientPortalUser(Client $client): User
{
    $user = User::factory()->create(['client_id' => $client->id]);
    $user->assignRole('client');

    return $user;
}

test('client cannot view another clients shared task', function () {
    $clientA = Client::factory()->create();
    $clientB = Client::factory()->create();
    $task    = sharedTaskFor($clientA);
    $userB   = clientPortalUser($clientB);

    $this->actingAs($userB)
        ->get(route('client.content.show', $task))
        ->assertNotFound();
});

test('client cannot approve another clients task', function () {
    $clientA = Client::factory()->create();
    $clientB = Client::factory()->create();
    $task    = sharedTaskFor($clientA);
    $userB   = clientPortalUser($clientB);

    $this->actingAs($userB)
        ->post(route('client.content.approve', $task))
        ->assertNotFound();

    expect($task->fresh()->client_approved_at)->toBeNull();
});

test('client cannot view content that has not been shared yet', function () {
    $client  = Client::factory()->create();
    $project = Project::factory()->create(['client_id' => $client->id]);
    $task    = Task::factory()->create(['project_id' => $project->id]);
    $user    = clientPortalUser($client);

    $this->actingAs($user)
        ->get(route('client.content.show', $task))
        ->assertNotFound();
});

test('non pm-or-admin cannot share a task with the client', function () {
    $client  = Client::factory()->create();
    $project = Project::factory()->create(['client_id' => $client->id]);
    $task    = Task::factory()->create(['project_id' => $project->id]);
    $user    = User::factory()->create();

    $this->actingAs($user)
        ->post(route('tasks.share-with-client', $task))
        ->assertForbidden();

    expect($task->fresh()->shared_with_client_at)->toBeNull();
});

test('project manager can share a task with the client and the client is notified', function () {
    Notification::fake();

    $client     = Client::factory()->create();
    $project    = Project::factory()->create(['client_id' => $client->id]);
    $task       = Task::factory()->create(['project_id' => $project->id]);
    $clientUser = clientPortalUser($client);

    $pm = User::factory()->create();
    $pm->assignRole('project-manager');

    $this->actingAs($pm)
        ->post(route('tasks.share-with-client', $task))
        ->assertRedirect();

    expect($task->fresh()->shared_with_client_at)->not->toBeNull();
    Notification::assertSentTo($clientUser, ContentReadyForApproval::class);
});

test('client can approve shared content', function () {
    $client     = Client::factory()->create();
    $task       = sharedTaskFor($client);
    $clientUser = clientPortalUser($client);

    $this->actingAs($clientUser)
        ->post(route('client.content.approve', $task))
        ->assertRedirect(route('client.content.show', $task));

    $task->refresh();
    expect($task->client_approved_at)->not->toBeNull();
    expect($task->status)->toBe(TaskStatus::Done->value);
});

test('client can request a revision which reopens the task and posts a visible comment', function () {
    $client     = Client::factory()->create();
    $task       = sharedTaskFor($client, ['client_approved_at' => now()]);
    $clientUser = clientPortalUser($client);

    $this->actingAs($clientUser)
        ->post(route('client.content.request-revision', $task), ['body' => 'Please change the color to match our brand.'])
        ->assertRedirect(route('client.content.show', $task));

    $task->refresh();
    expect($task->status)->toBe(TaskStatus::Doing->value);
    expect($task->client_approved_at)->toBeNull();
    expect($task->visibleComments()->count())->toBe(1);
    expect($task->visibleComments()->first()->body)->toBe('Please change the color to match our brand.');
});

test('revision request requires a body or a file', function () {
    $client     = Client::factory()->create();
    $task       = sharedTaskFor($client);
    $clientUser = clientPortalUser($client);

    $this->actingAs($clientUser)
        ->post(route('client.content.request-revision', $task), ['body' => ''])
        ->assertSessionHasErrors('body');

    expect($task->fresh()->status)->toBe(TaskStatus::Review->value);
});
