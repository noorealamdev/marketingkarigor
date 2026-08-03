<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role;

class Invitation extends Model
{
    protected $fillable = [
        'email', 'name', 'token', 'role_ids', 'client_id', 'invited_by', 'accepted_at', 'expires_at',
    ];

    protected $casts = [
        'role_ids'    => 'array',
        'accepted_at' => 'datetime',
        'expires_at'  => 'datetime',
    ];

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * role_ids stores Spatie role *names* (not numeric IDs) — Spatie role IDs
     * aren't stable across environments, but role names are.
     */
    public function roles()
    {
        return Role::whereIn('name', $this->role_ids ?? [])->get();
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return !is_null($this->accepted_at);
    }
}
