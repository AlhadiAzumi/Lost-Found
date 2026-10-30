<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model {
    protected $fillable = [
        'user_id', 'category_id', 'type', 'title', 'description', 
        'location', 'latitude', 'longitude', 'date_event', 'image', 
        'is_sensitive', 'security_question', 'status', 'approved_by'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
    public function claims() { return $this->hasMany(Claim::class); }
}