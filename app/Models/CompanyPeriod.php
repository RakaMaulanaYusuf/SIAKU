<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyPeriod extends Model
{
    protected $table = 'company_period';
    
    protected $fillable = [
        'company_id',
        'period_month',
        'period_year'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'company_period_id');
    }
}
