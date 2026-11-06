<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function getColorAttribute(): string
    {
        $map = [
            'elektronik' => '#fce7f6',
            'dompet'     => '#dbeafe',
            'kunci'      => '#ecfccb',
            'tas'        => '#d1fae8',
            'alat-tulis' => '#ede9fe',
            'dokumen'    => '#fefbec',
            'pakaian'    => '#ffedd5',
        ];

        return $map[$this->slug] ?? '#fee2e2';
    }
}
