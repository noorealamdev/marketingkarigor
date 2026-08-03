<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MemberProfile extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'phone',
        'date_of_birth',
        'address',
        'nid_number',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'bank_name',
        'bank_account_holder',
        'bank_account_number',
        'bank_branch',
        'bank_routing_number',
        'admin_notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('personal_documents');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
