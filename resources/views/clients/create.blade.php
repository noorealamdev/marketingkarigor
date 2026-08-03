@extends('layouts.app')
@section('title', 'New Client')
@section('breadcrumb', 'Clients / New Client')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Add New Client</h2>
        <p>Fill in the details to create a new client profile.</p>
    </div>
    <a href="{{ route('clients.index') }}" class="btn btn-secondary">← Back</a>
</div>

<div class="card" style="max-width:680px;">
    <div class="card-bd">
        <form method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Jane Smith" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Company</label>
                    <input type="text" name="company" class="form-control" value="{{ old('company') }}" placeholder="Acme Corp">
                    @error('company')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="jane@example.com">
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+1 555-0100">
                    @error('phone')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-control">
                        <option value="active" {{ old('status','active')=='active'?'selected':'' }}>Active</option>
                        <option value="prospect" {{ old('status')=='prospect'?'selected':'' }}>Prospect</option>
                        <option value="inactive" {{ old('status')=='inactive'?'selected':'' }}>Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Website</label>
                    <input type="url" name="website" class="form-control" value="{{ old('website') }}" placeholder="https://example.com">
                    @error('website')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2" placeholder="Street address, city, state, zip">{{ old('address') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Internal notes about this client…">{{ old('notes') }}</textarea>
            </div>

            <div class="page-hd" style="margin:20px 0 12px;">
                <h3 style="font-size:1rem;">Brand Kit</h3>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">WhatsApp</label>
                    <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp') }}" placeholder="+1 555-0100">
                    @error('whatsapp')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Facebook Page</label>
                    <input type="url" name="facebook_page" class="form-control" value="{{ old('facebook_page') }}" placeholder="https://facebook.com/acme">
                    @error('facebook_page')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Brand Colors <span class="text-faint text-xs">(comma-separated hex codes)</span></label>
                    <input type="text" name="brand_colors" class="form-control" value="{{ old('brand_colors') }}" placeholder="#6c63ff, #0d0f14">
                </div>
                <div class="form-group">
                    <label class="form-label">Fonts <span class="text-faint text-xs">(comma-separated)</span></label>
                    <input type="text" name="fonts" class="form-control" value="{{ old('fonts') }}" placeholder="Inter, Poppins">
                </div>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Package</label>
                    <input type="text" name="package" class="form-control" value="{{ old('package') }}" placeholder="Growth Plan">
                </div>
                <div class="form-group">
                    <label class="form-label">Renewal Date</label>
                    <input type="date" name="renewal_date" class="form-control" value="{{ old('renewal_date') }}">
                    @error('renewal_date')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Logo</label>
                <input type="file" name="logo" class="form-control" accept="image/*">
                @error('logo')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="row" style="gap:8px;margin-top:4px;">
                <button type="submit" class="btn btn-primary">Create Client</button>
                <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
