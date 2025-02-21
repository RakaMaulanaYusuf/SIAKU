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
}