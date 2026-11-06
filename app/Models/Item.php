<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
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
        'status',
        'approved_by',
    ];

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

    public function getPhotoUrlAttribute(): string
    {
        if (!empty($this->image)) {
            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                return $this->image;
            }
            return Storage::url($this->image);
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
}
