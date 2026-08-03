@extends('layouts.app')
@section('title', 'Edit Client')
@section('breadcrumb', 'Clients / Edit')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Edit Client</h2>
        <p>{{ $client->name }}</p>
    </div>
    <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">← Back</a>
</div>

<div class="card" style="max-width:680px;">
    <div class="card-bd">
        <form method="POST" action="{{ route('clients.update', $client) }}" enctype="multipart/form-data">
            @csrf @method('PATCH')
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $client->name) }}" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Company</label>
                    <input type="text" name="company" class="form-control" value="{{ old('company', $client->company) }}">
                </div>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $client->email) }}">
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $client->phone) }}">
                </div>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-control">
                        <option value="active" {{ old('status',$client->status)=='active'?'selected':'' }}>Active</option>
                        <option value="prospect" {{ old('status',$client->status)=='prospect'?'selected':'' }}>Prospect</option>
                        <option value="inactive" {{ old('status',$client->status)=='inactive'?'selected':'' }}>Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Website</label>
                    <input type="url" name="website" class="form-control" value="{{ old('website', $client->website) }}">
                    @error('website')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address', $client->address) }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $client->notes) }}</textarea>
            </div>

            <div class="page-hd" style="margin:20px 0 12px;">
                <h3 style="font-size:1rem;">Brand Kit</h3>
            </div>
            <div class="form-group">
                <label class="form-label">Logo</label>
                @if($client->getFirstMediaUrl('logo'))
                    <div style="margin-bottom:8px;">
                        <img src="{{ $client->getFirstMediaUrl('logo') }}" alt="Current logo" style="max-height:60px;border-radius:6px;">
                    </div>
                @endif
                <input type="file" name="logo" class="form-control" accept="image/*">
                <p class="text-faint text-xs" style="margin-top:4px;">{{ $client->getFirstMediaUrl('logo') ? 'Upload a new file to replace the current logo.' : 'PNG or JPG, max 4MB.' }}</p>
                @error('logo')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">WhatsApp</label>
                    <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $client->whatsapp) }}">
                    @error('whatsapp')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Facebook Page</label>
                    <input type="url" name="facebook_page" class="form-control" value="{{ old('facebook_page', $client->facebook_page) }}">
                    @error('facebook_page')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Brand Colors <span class="text-faint text-xs">(comma-separated hex codes)</span></label>
                    <input type="text" name="brand_colors" class="form-control" value="{{ old('brand_colors', implode(', ', $client->brand_colors ?? [])) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Fonts <span class="text-faint text-xs">(comma-separated)</span></label>
                    <input type="text" name="fonts" class="form-control" value="{{ old('fonts', implode(', ', $client->fonts ?? [])) }}">
                </div>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Package</label>
                    <input type="text" name="package" class="form-control" value="{{ old('package', $client->package) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Renewal Date</label>
                    <input type="date" name="renewal_date" class="form-control" value="{{ old('renewal_date', $client->renewal_date?->format('Y-m-d')) }}">
                    @error('renewal_date')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row" style="gap:8px;margin-top:4px;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
