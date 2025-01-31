<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'type',
        'status',
        'period_month',
        'period_year'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'active_company_id');
    }

    public function getInitialAttribute()
    {
        return substr($this->name, 3, 1);
    }

    // Scope untuk memfilter data berdasarkan company
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}