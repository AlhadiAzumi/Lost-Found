<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Handover extends Model {
    protected $fillable = ['claim_id', 'officer_id', 'receiver_identity_photo', 'handed_over_at', 'notes'];

    public function claim() { return $this->belongsTo(Claim::class); }
    public function officer() { return $this->belongsTo(User::class, 'officer_id'); }
}
