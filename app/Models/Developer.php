<?php
// app/Models/Developer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Developer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    // Scope untuk developer aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk developer tidak aktif
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
