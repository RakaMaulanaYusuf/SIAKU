<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KodeBantu extends Model
{
    protected $table = 'kode_bantu';
    
    protected $fillable = [
        'company_id',
        'company_period_id',  // Added this field
        'helper_id',
        'name',
        'status',
        'balance'
    ];

    protected $casts = [
        'status' => 'string',    // PIUTANG atau HUTANG 
        'balance' => 'decimal:2'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function period()
    {
        return $this->belongsTo(CompanyPeriod::class, 'company_period_id');
    }

    public function journalEntries()
    {
        return $this->hasMany(JurnalUmum::class, 'helper_id', 'helper_id');
    }
}