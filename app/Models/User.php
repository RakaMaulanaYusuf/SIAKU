<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable 
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'active_company_id', 'assigned_company_id',
        'company_period_id', 'assigned_company_period_id'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasRole($roleName): bool
    {
        return $this->role === $roleName;
    }
    
    public function activeCompany() {
        return $this->belongsTo(Company::class, 'active_company_id');
    }

    public function assignedCompany() {
        return $this->belongsTo(Company::class, 'assigned_company_id');
    }

    public function activePeriod() {
        return $this->belongsTo(CompanyPeriod::class, 'company_period_id');
    }

    public function assignedPeriod() {
        return $this->belongsTo(CompanyPeriod::class, 'assigned_company_period_id');
    }
}