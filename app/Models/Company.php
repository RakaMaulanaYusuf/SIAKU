<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'type', 
        'address',
        'phone',
        'email',
        'status'
    ];

    public function periods()
    {
        return $this->hasMany(CompanyPeriod::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'active_company_id');
    }

    // Relasi untuk viewers yang di-assign ke company ini
    // public function assignedViewers()
    // {
    //     return $this->hasMany(User::class, 'assigned_company_id')->where('role', 'viewer');
    // }

    // Scope untuk mendapatkan companies dengan viewer yang sudah di-assign
    // public function scopeWithAssignedViewers($query)
    // {
    //     return $query->with('assignedViewers');
    // }

    // Accessor untuk mendapatkan jumlah viewer yang di-assign
    // public function getAssignedViewersCountAttribute()
    // {
    //     return $this->assignedViewers()->count();
    // }

    // Accessor untuk status yang lebih readable
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => 'bg-green-100 text-green-800',
            'inactive' => 'bg-red-100 text-red-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}