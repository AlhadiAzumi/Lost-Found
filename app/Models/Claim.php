<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model {
    protected $fillable = [
        'item_id', 'user_id', 'security_answer', 'proof_image', 
        'status', 'handover_code', 'verified_by', 'admin_note'
    ];

    public function item() { return $this->belongsTo(Item::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function verifier() { return $this->belongsTo(User::class, 'verified_by'); }
    public function handover() { return $this->hasOne(Handover::class); }
}