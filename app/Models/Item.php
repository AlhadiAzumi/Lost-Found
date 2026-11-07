<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCampus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    use BelongsToCampus;

    protected $fillable = [
        'user_id',
        'category_id',
        'type',
        'title',
        'description',
        'location',
        'latitude',
        'longitude',
        'date_event',
        'image',
        'is_sensitive',
        'security_question',
        'security_answer',
        'rejection_reason',
        'status',
        'approved_by',
    ];

    protected $hidden = ['security_answer'];

    protected $casts = [
        'date_event' => 'date',
        'is_sensitive' => 'boolean',
    ];

    // Alias getters for view compatibility
    public function getNameAttribute(): string
    {
        return $this->title ?? '';
    }

    public function getOccurredOnAttribute()
    {
        return $this->date_event;
    }

    public function setSecurityAnswerAttribute(?string $answer): void
    {
        $this->attributes['security_answer'] = filled($answer)
            ? Hash::make(mb_strtolower(trim($answer)))
            : null;
    }

    public function hasSecurityAnswer(): bool
    {
        return filled($this->attributes['security_answer'] ?? null);
    }

    public function matchesSecurityAnswer(string $answer): ?bool
    {
        if (! $this->hasSecurityAnswer()) {
            return null;
        }

        return Hash::check(mb_strtolower(trim($answer)), $this->attributes['security_answer']);
    }

    public function getPhotoUrlAttribute(): string
    {
        if (! empty($this->image)) {
            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                return $this->image;
            }

            return Storage::disk('public')->url($this->image);
        }

        return asset('images/placeholder-item.png');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->whereIn('status', ['approved', 'disetujui']);
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->whereIn('status', ['approved', 'claim_in_progress', 'returned']);
    }
}
