@extends('layouts.app')
@section('title', 'Edit Invoice')
@section('breadcrumb', 'Invoices / Edit')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Edit Invoice</h2>
        <p>{{ $invoice->invoice_number }}</p>
    </div>
    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">← Back</a>
</div>

<div class="card" style="max-width:680px;">
    <div class="card-bd">
        <form method="POST" action="{{ route('invoices.update', $invoice) }}">
            @csrf @method('PATCH')
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Invoice Number *</label>
                    <input type="text" name="invoice_number" class="form-control" value="{{ old('invoice_number', $invoice->invoice_number) }}" required>
                    @error('invoice_number')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Amount (৳) *</label>
                    <input type="number" name="amount" class="form-control" value="{{ old('amount', $invoice->amount) }}" step="0.01" min="0" required>
                </div>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Client</label>
                    <x-client-picker :clients="$clients" :selected="old('client_id', $invoice->client_id)" empty-option="— No Client —" />
                </div>
                <div class="form-group">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-control">
                        <option value="">— No Project —</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" data-client-id="{{ $project->client_id }}" {{ old('project_id',$invoice->project_id)==$project->id?'selected':'' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-control">
                        <option value="draft" {{ old('status',$invoice->status)=='draft'?'selected':'' }}>Draft</option>
                        <option value="sent" {{ old('status',$invoice->status)=='sent'?'selected':'' }}>Sent</option>
                        <option value="paid" {{ old('status',$invoice->status)=='paid'?'selected':'' }}>Paid</option>
                        <option value="overdue" {{ old('status',$invoice->status)=='overdue'?'selected':'' }}>Overdue</option>
                        <option value="cancelled" {{ old('status',$invoice->status)=='cancelled'?'selected':'' }}>Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Issued Date</label>
                    <input type="date" name="issued_date" class="form-control" value="{{ old('issued_date', $invoice->issued_date?->format('Y-m-d')) }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Due Date</label>
                <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $invoice->due_date?->format('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $invoice->notes) }}</textarea>
            </div>
            <div class="row" style="gap:8px;margin-top:4px;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var clientHidden  = document.querySelector('.client-picker-value');
    var projectSelect = document.querySelector('select[name="project_id"]');
    if (!clientHidden || !projectSelect) return;

    function filterProjects() {
        var clientId = clientHidden.value;
        var currentValid = false;
        projectSelect.querySelectorAll('option[data-client-id]').forEach(function(opt) {
            var match = !clientId || opt.dataset.clientId === clientId;
            opt.hidden = !match;
            if (match && opt.value === projectSelect.value) currentValid = true;
        });
        if (!currentValid) projectSelect.value = '';
    }

    clientHidden.addEventListener('change', filterProjects);
    filterProjects();
})();
</script>
@endpush
@endsection
