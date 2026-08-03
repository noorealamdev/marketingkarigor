@extends('layouts.portal')
@section('title', 'Content')

@section('content')
<div class="page-hd">
    <h2>Content for Review</h2>
    <p>Content shared with you by the {{ config('app.name') }} team.</p>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Content</th>
                <th>Project</th>
                <th>Status</th>
                <th>Shared</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
            <tr>
                <td class="td-header">{{ $task->name }}</td>
                <td class="text-muted" data-label="Project">{{ $task->project?->name ?? '—' }}</td>
                <td data-label="Status">
                    @php $label = $task->clientApprovalLabel(); @endphp
                    @if($label === 'Approved')
                        <span class="badge badge-done">Approved</span>
                    @else
                        <span class="badge badge-review">Awaiting Your Approval</span>
                    @endif
                </td>
                <td class="text-muted" data-label="Shared">{{ $task->shared_with_client_at->format('M d, Y') }}</td>
                <td class="td-actions"><a href="{{ route('client.content.show', $task) }}" class="btn btn-secondary btn-sm">View →</a></td>
            </tr>
            @empty
            <tr><td colspan="5" class="td-empty">
                <div class="empty-state"><p>Nothing has been shared with you for review yet.</p></div>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($tasks->hasPages())
<div class="pager">{{ $tasks->links() }}</div>
@endif
@endsection
