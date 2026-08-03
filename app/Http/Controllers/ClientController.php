<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::withCount('projects');
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('company', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");

                if (is_numeric($request->search)) {
                    $q->orWhere('id', (int) $request->search);
                }
            });
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        $clients = $query->latest()->paginate(12);
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $client = Client::create($data);

        if ($request->hasFile('logo')) {
            $client->addMediaFromRequest('logo')->toMediaCollection('logo');
        }

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        $client->load(['projects', 'invoices']);
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $this->validated($request);
        $client->update($data);

        if ($request->hasFile('logo')) {
            $client->addMediaFromRequest('logo')->toMediaCollection('logo');
        }

        return redirect()->route('clients.show', $client)->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|string|max:50',
            'whatsapp'      => 'nullable|string|max:50',
            'facebook_page' => 'nullable|url|max:255',
            'company'       => 'nullable|string|max:255',
            'address'       => 'nullable|string',
            'status'        => 'required|in:active,inactive,prospect',
            'notes'         => 'nullable|string',
            'website'       => 'nullable|url|max:255',
            'brand_colors'  => 'nullable|string',
            'fonts'         => 'nullable|string',
            'package'       => 'nullable|string|max:255',
            'renewal_date'  => 'nullable|date',
            'logo'          => 'nullable|image|max:4096',
        ]);

        $data['brand_colors'] = $this->splitCsv($data['brand_colors'] ?? null);
        $data['fonts']        = $this->splitCsv($data['fonts'] ?? null);
        unset($data['logo']);

        return $data;
    }

    private function splitCsv(?string $value): array
    {
        if (!$value) {
            return [];
        }
        return collect(explode(',', $value))->map(fn ($v) => trim($v))->filter()->values()->all();
    }
}
