<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCampus;

class Handover extends Model
{
    use BelongsToCampus;

    protected $fillable = [
        'claim_id',
        'item_id',
        'claimed_by',
        'handed_over_by',
        'handover_date',
        'proof_photo',
        'notes',
    ];

    protected $casts = [
        'handover_date' => 'datetime',
    ];

    public function claim()
    {
        return $this->belongsTo(Claim::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'handed_over_by');
    }
}
