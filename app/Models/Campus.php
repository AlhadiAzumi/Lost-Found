<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Campus extends Model
{
    protected $fillable = ['name', 'slug', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function formatName(string $name): string
    {
        $name = preg_replace('/\s+/u', ' ', trim($name)) ?? trim($name);

        return mb_strtolower($name, 'UTF-8') === $name
            ? Str::title($name)
            : $name;
    }

    public function getNameAttribute(?string $value): ?string
    {
        return $value === null ? null : self::formatName($value);
    }

    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = self::formatName($value);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
