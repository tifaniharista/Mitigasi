<?php
// app/Models/Opd.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opd extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function projects()
    {
        return $this->hasMany(Project::class, 'opd', 'name');
    }

    // Scope untuk OPD aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk OPD tidak aktif
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
