@extends('layouts.app')
@section('title', $user->name)
@section('breadcrumb', 'Team / ' . $user->name)

@section('content')
<div class="page-hd row between">
    <div class="row center" style="gap:14px;">
        {!! user_avatar($user, 52) !!}
        <div>
            <h2 style="margin-bottom:4px;">{{ $user->name }}</h2>
            <div class="text-sm text-muted">{{ $user->email }}</div>
            <div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:6px;">
                @forelse($user->roles as $role)
                    <span class="badge" style="background:rgba(108,99,255,0.12);color:#a89fff;">{{ $role->name }}</span>
                @empty
                    <span class="text-xs text-faint">No roles</span>
                @endforelse
            </div>
        </div>
    </div>
    <div class="row" style="gap:8px;">
        <form method="POST" action="{{ route('team.reset-link', $user) }}" id="resetLinkForm">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Generate a new password reset link for {{ addslashes($user->name) }}?')">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Reset Password
            </button>
        </form>
        @if($user->id !== auth()->id())
        <form method="POST" action="{{ route('team.destroy', $user) }}" onsubmit="return confirm('Remove {{ addslashes($user->name) }} from the team? This cannot be undone.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                Remove
            </button>
        </form>
        @endif
        <a href="{{ route('team.index') }}" class="btn btn-secondary btn-sm">← Team</a>
    </div>
</div>

{{-- Reset Link Modal --}}
@if(session('reset_link'))
<div id="resetModal" style="position:fixed;inset:0;z-index:300;background:rgba(0,0,0,0.65);display:flex;align-items:center;justify-content:center;">
    <div style="background:#13161d;border:1px solid #252936;border-radius:14px;padding:28px 28px 24px;max-width:520px;width:90%;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
            <div style="width:34px;height:34px;background:rgba(108,99,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6c63ff" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <h3 style="font-size:1rem;font-weight:700;color:#c8cce0;">Password Reset Link</h3>
        </div>
        <p style="font-size:0.845rem;color:#6b7590;margin-bottom:18px;line-height:1.6;">
            Copy this link and send it to <strong style="color:#c8cce0;">{{ session('reset_name') }}</strong>. It expires in 60 minutes. Keep it private.
        </p>
        <div style="background:#0a0c12;border:1px solid #252936;border-radius:8px;padding:12px 14px;display:flex;align-items:center;gap:10px;margin-bottom:18px;">
            <input id="resetLinkInput" type="text" value="{{ session('reset_link') }}" readonly
                   style="flex:1;background:transparent;border:none;outline:none;font-size:0.78rem;color:#8a91b0;font-family:monospace;min-width:0;overflow:hidden;text-overflow:ellipsis;">
            <button onclick="copyResetLink()" id="copyBtn"
                    style="flex-shrink:0;background:#6c63ff;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:0.78rem;font-weight:700;cursor:pointer;white-space:nowrap;">
                Copy
            </button>
        </div>
        @php
            $mailDriver = config('mail.default', 'log');
            $mailHost   = config("mail.mailers.{$mailDriver}.host") ?? config('mail.mailers.smtp.host');
            $mailUser   = config("mail.mailers.{$mailDriver}.username") ?? config('mail.mailers.smtp.username');
            $mailConfigured = !empty($mailHost) && !empty($mailUser) && $mailDriver !== 'log';
        @endphp
        @if(!$mailConfigured)
        <div style="background:rgba(251,191,36,0.07);border:1px solid rgba(251,191,36,0.2);border-radius:8px;padding:10px 14px;font-size:0.78rem;color:#d97706;margin-bottom:18px;line-height:1.5;">
            ⚠️ Mail is not configured — you must copy and share this link manually.
        </div>
        @else
        <div style="background:rgba(74,222,128,0.06);border:1px solid rgba(74,222,128,0.2);border-radius:8px;padding:10px 14px;font-size:0.78rem;color:#4ade80;margin-bottom:18px;line-height:1.5;">
            ✓ Mail is configured — you can also email this link directly to the member.
        </div>
        @endif
        <button onclick="document.getElementById('resetModal').remove()" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;">
            Close
        </button>
    </div>
</div>
<script>
function copyResetLink() {
    const input = document.getElementById('resetLinkInput');
    input.select();
    navigator.clipboard.writeText(input.value).then(() => {
        const btn = document.getElementById('copyBtn');
        btn.textContent = 'Copied!';
        btn.style.background = '#4ade80';
        btn.style.color = '#0a0c12';
        setTimeout(() => { btn.textContent = 'Copy'; btn.style.background = '#6c63ff'; btn.style.color = '#fff'; }, 2000);
    }).catch(() => { document.execCommand('copy'); });
}
</script>
@endif

{{-- Stats Cards --}}
<div class="g4 mb6">
    <div class="stat-card">
        <div class="stat-label">This Month Done</div>
        <div class="stat-value" style="color:#4ade80;">{{ $currentMonthDone }}</div>
        <div class="stat-sub">{{ now()->format('F Y') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Last Month Done</div>
        <div class="stat-value" style="color:#60a5fa;">{{ $previousMonthDone }}</div>
        <div class="stat-sub">{{ now()->subMonth()->format('F Y') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Active Tasks</div>
        <div class="stat-value">{{ $activeTasks->count() }}</div>
        <div class="stat-sub">Not yet completed</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">All-Time Done</div>
        <div class="stat-value">{{ $user->assignedTasks->where('status', \App\Enums\TaskStatus::Done->value)->count() }}</div>
        <div class="stat-sub">Total completed</div>
    </div>
</div>

<div class="g2 mb6">
    {{-- Monthly Task Stats --}}
    <div class="card">
        <div class="card-hd"><h3>Task Completion — Last 6 Months</h3></div>
        <table class="table">
            <thead>
                <tr><th>Month</th><th>Completed</th><th>In Progress</th><th>Total</th><th>Rate</th></tr>
            </thead>
            <tbody>
                @foreach($months as $m)
                <tr>
                    <td class="fw600 td-header">{{ $m['label'] }}</td>
                    <td data-label="Completed"><span style="color:#4ade80;font-weight:700;">{{ $m['completed'] }}</span></td>
                    <td data-label="In Progress"><span style="color:#60a5fa;">{{ $m['in_progress'] }}</span></td>
                    <td class="text-muted" data-label="Total">{{ $m['total'] }}</td>
                    <td data-label="Rate">
                        @if($m['total'] > 0)
                            @php $rate = round($m['completed']/$m['total']*100) @endphp
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div class="progress-bar" style="width:60px;">
                                    <div class="progress-fill" style="width:{{ $rate }}%;background:{{ $rate>=80 ? '#4ade80' : ($rate>=50 ? '#fbbf24' : '#f87171') }};"></div>
                                </div>
                                <span class="text-xs">{{ $rate }}%</span>
                            </div>
                        @else
                            <span class="text-faint text-xs">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Role Management --}}
    <div>
        <div class="card mb4">
            <div class="card-hd"><h3>Manage Roles</h3></div>
            <div class="card-bd">
                <form method="POST" action="{{ route('team.update-roles', $user) }}">
                    @csrf
                    <p class="text-sm text-muted" style="margin-bottom:12px;">A member can hold multiple roles. Check all that apply.</p>
                    @foreach($roles as $role)
                    <label style="display:flex;align-items:flex-start;gap:10px;padding:10px;border-radius:7px;cursor:pointer;border:1px solid {{ $user->roles->contains($role) ? 'rgba(108,99,255,0.4)' : '#252936' }};background:{{ $user->roles->contains($role) ? 'rgba(108,99,255,0.08)' : 'transparent' }};margin-bottom:6px;">
                        <input type="checkbox" name="role_ids[]" value="{{ $role->id }}" {{ $user->roles->contains($role) ? 'checked' : '' }} style="margin-top:2px;accent-color:#6c63ff;">
                        <div>
                            <div class="fw600" style="font-size:0.875rem;">{{ $role->name }}</div>
                            @if($role->description)
                            <div class="text-xs text-muted">{{ $role->description }}</div>
                            @endif
                        </div>
                    </label>
                    @endforeach
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:10px;">Update Roles</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Active Tasks --}}
<div class="card mb6">
    <div class="card-hd"><h3>Active Tasks ({{ $activeTasks->count() }})</h3></div>
    @if($activeTasks->isEmpty())
        <div class="empty-state"><p>No active tasks assigned to {{ $user->name }}.</p></div>
    @else
    <table class="table">
        <thead><tr><th>Task</th><th>Project</th><th>Status</th><th>Priority</th><th>Due Date</th></tr></thead>
        <tbody>
            @foreach($activeTasks as $task)
            <tr>
                <td class="td-header"><a href="{{ route('tasks.show', $task) }}" class="fw600 link">{{ $task->name }}</a></td>
                <td class="text-muted" data-label="Project">{{ $task->project?->name ?? '—' }}</td>
                <td data-label="Status"><span class="badge badge-{{ $task->status }}">{{ str_replace('_',' ',$task->status) }}</span></td>
                <td data-label="Priority"><span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span></td>
                <td data-label="Due Date" style="{{ $task->due_date?->isPast() ? 'color:#f87171' : 'color:#6b7590' }}">
                    {{ $task->due_date?->format('M d, Y') ?? '—' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

{{-- ══════════════════════════════════════════════
     PERSONAL & HR INFORMATION  (admin-only section)
     ══════════════════════════════════════════════ --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
    <div style="flex:1;height:1px;background:#252936;"></div>
    <div style="display:flex;align-items:center;gap:8px;padding:6px 14px;background:#1a1e28;border:1px solid #252936;border-radius:999px;font-size:0.72rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#4a5068;">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#6c63ff" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Personal &amp; HR — Admin Only
    </div>
    <div style="flex:1;height:1px;background:#252936;"></div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

    {{-- Personal Information --}}
    <div class="card">
        <div class="card-hd">
            <h3 style="display:flex;align-items:center;gap:8px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6c63ff" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Personal Information
            </h3>
        </div>
        <div class="card-bd">
            <form method="POST" action="{{ route('team.profile.update', $user) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $profile?->phone) }}" placeholder="+1 555 000 0000">
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $profile?->date_of_birth?->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Full address">{{ old('address', $profile?->address) }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">NID / National ID Number</label>
                    <input type="text" name="nid_number" class="form-control" value="{{ old('nid_number', $profile?->nid_number) }}" placeholder="ID number">
                </div>
                <div style="border-top:1px solid #252936;padding-top:14px;margin-top:4px;">
                    <div class="text-xs text-faint" style="margin-bottom:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;">Emergency Contact</div>
                    <div class="form-group">
                        <label class="form-label">Name</label>
                        <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $profile?->emergency_contact_name) }}">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone', $profile?->emergency_contact_phone) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Relation</label>
                            <input type="text" name="emergency_contact_relation" class="form-control" value="{{ old('emergency_contact_relation', $profile?->emergency_contact_relation) }}" placeholder="e.g. Spouse">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;margin-top:6px;">Save Personal Info</button>
            </form>
        </div>
    </div>

    {{-- Bank & Notes --}}
    <div style="display:flex;flex-direction:column;gap:20px;">
        <div class="card" style="flex:1;">
            <div class="card-hd">
                <h3 style="display:flex;align-items:center;gap:8px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6c63ff" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    Bank Account
                </h3>
            </div>
            <div class="card-bd">
                <form method="POST" action="{{ route('team.profile.update', $user) }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $profile?->bank_name) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Account Holder Name</label>
                        <input type="text" name="bank_account_holder" class="form-control" value="{{ old('bank_account_holder', $profile?->bank_account_holder) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Account Number</label>
                        <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number', $profile?->bank_account_number) }}" placeholder="••••••••">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div class="form-group">
                            <label class="form-label">Branch</label>
                            <input type="text" name="bank_branch" class="form-control" value="{{ old('bank_branch', $profile?->bank_branch) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Routing Number</label>
                            <input type="text" name="bank_routing_number" class="form-control" value="{{ old('bank_routing_number', $profile?->bank_routing_number) }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;margin-top:2px;">Save Bank Info</button>
                </form>
            </div>
        </div>

        {{-- Admin Notes --}}
        <div class="card">
            <div class="card-hd">
                <h3 style="display:flex;align-items:center;gap:8px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6c63ff" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Admin Notes
                </h3>
            </div>
            <div class="card-bd">
                <form method="POST" action="{{ route('team.profile.update', $user) }}">
                    @csrf
                    <div class="form-group" style="margin-bottom:10px;">
                        <textarea name="admin_notes" class="form-control" rows="4" placeholder="Private notes — only visible to admins…">{{ old('admin_notes', $profile?->admin_notes) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;">Save Notes</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Documents --}}
<div class="card">
    <div class="card-hd" style="justify-content:space-between;">
        <h3 style="display:flex;align-items:center;gap:8px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6c63ff" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Documents
            <span style="background:#1a1e28;color:#6b7590;font-size:0.7rem;padding:1px 8px;border-radius:999px;font-weight:600;">{{ $documents->count() }}</span>
        </h3>
        <button onclick="document.getElementById('docUploadPanel').classList.toggle('hidden')" class="btn btn-secondary btn-sm">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Upload
        </button>
    </div>

    {{-- Upload form --}}
    <div id="docUploadPanel" class="hidden" style="padding:16px 18px;border-bottom:1px solid #252936;background:#0d0f14;">
        <form method="POST" action="{{ route('team.profile.documents.store', $user) }}" enctype="multipart/form-data">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:10px;align-items:flex-end;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label">File</label>
                    <input type="file" name="document" class="form-control" required>
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Label (optional)</label>
                    <input type="text" name="document_label" class="form-control" placeholder="e.g. NID Card Front">
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Upload</button>
            </div>
        </form>
    </div>

    {{-- Document list --}}
    @if($documents->isEmpty())
        <div class="empty-state"><p>No documents uploaded yet.</p></div>
    @else
    <table class="table">
        <thead><tr><th>Name</th><th>Type</th><th>Size</th><th>Uploaded</th><th></th></tr></thead>
        <tbody>
            @foreach($documents as $doc)
            <tr>
                <td class="td-header">
                    <div style="display:flex;align-items:center;gap:9px;">
                        @php
                            $isImage = str_starts_with($doc->mime_type, 'image/');
                            $isPdf   = $doc->mime_type === 'application/pdf';
                        @endphp
                        <div style="width:30px;height:30px;border-radius:6px;background:#1a1e28;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            @if($isImage)
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            @elseif($isPdf)
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            @else
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#6b7590" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            @endif
                        </div>
                        <span class="fw600" style="font-size:0.845rem;">{{ $doc->name }}</span>
                    </div>
                </td>
                <td class="text-muted text-xs" data-label="Type">{{ strtoupper(pathinfo($doc->file_name, PATHINFO_EXTENSION)) }}</td>
                <td class="text-muted text-xs" data-label="Size">{{ number_format($doc->size / 1024, 1) }} KB</td>
                <td class="text-muted text-xs" data-label="Uploaded">{{ $doc->created_at->format('M d, Y') }}</td>
                <td class="td-actions">
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                        <a href="{{ route('team.profile.documents.download', [$user, $doc]) }}" class="btn btn-secondary btn-xs">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Download
                        </a>
                        <form method="POST" action="{{ route('team.profile.documents.destroy', [$user, $doc]) }}" onsubmit="return confirm('Delete this document?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

<style>
.hidden { display: none !important; }
</style>
@endsection
