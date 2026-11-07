<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampusApplication extends Model
{
    protected $fillable = [
        'name',
        'email',
        'slug',
        'status',
        'campus_id',
        'reviewed_by',
        'reviewed_at',
        'invitation_token_hash',
        'invitation_expires_at',
        'admin_created_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'invitation_expires_at' => 'datetime',
            'admin_created_at' => 'datetime',
        ];
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }
}
