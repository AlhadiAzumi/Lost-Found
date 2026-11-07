<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCampus;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use BelongsToCampus;

    private ?bool $securityAnswerMatch = null;

    private bool $securityAnswerMatchResolved = false;

    protected $fillable = [
        'item_id',
        'user_id',
        'security_answer',
        'proof_image',
        'status',
        'handover_code',
        'verified_by',
        'admin_note',
    ];

    protected $hidden = [
        'security_answer',
        'handover_code',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Compare the claimant's answer with the stored hash.
     *
     * bcrypt is intentionally expensive, so the result is memoized for the
     * current request. Views should call this instead of matching twice.
     */
    public function securityAnswerMatches(): ?bool
    {
        if (! $this->securityAnswerMatchResolved) {
            $this->securityAnswerMatch = $this->item?->matchesSecurityAnswer((string) $this->security_answer);
            $this->securityAnswerMatchResolved = true;
        }

        return $this->securityAnswerMatch;
    }

    public function handover()
    {
        return $this->hasOne(Handover::class);
    }
}
