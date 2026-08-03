@props([
    'clients',
    'name' => 'client_id',
    'selected' => null,
    'required' => false,
    'emptyOption' => null,
    'placeholder' => 'Search clients…',
])

@php
    $labelFor = fn ($client) => $client->company ? "{$client->company} ({$client->name})" : $client->name;
    $selectedClient = $selected ? $clients->firstWhere('id', (int) $selected) : null;
@endphp

<div class="client-picker">
    <input type="hidden" name="{{ $name }}" class="client-picker-value" value="{{ $selectedClient?->id }}">
    <div class="client-picker-input-wrap">
        <input type="text" class="form-control client-picker-search" autocomplete="off"
               placeholder="{{ $placeholder }}"
               value="{{ $selectedClient ? $labelFor($selectedClient) : '' }}"
               {{ $required ? 'required' : '' }}>
        <button type="button" class="client-picker-clear" title="Clear" style="display:{{ $selectedClient ? 'flex' : 'none' }};">&times;</button>
    </div>
    <div class="client-picker-dropdown">
        @if($emptyOption)
        <div class="client-picker-option" data-id="" data-label="{{ $emptyOption }}">{{ $emptyOption }}</div>
        @endif
        @foreach($clients as $client)
        <div class="client-picker-option" data-id="{{ $client->id }}" data-label="{{ $labelFor($client) }}">{{ $labelFor($client) }}</div>
        @endforeach
        <div class="client-picker-empty">No clients found</div>
    </div>
</div>
