<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KodeAkun extends Model
{
    protected $table = 'kode_akun';
    
    protected $fillable = [
        'company_id',
        'company_period_id',  // Added this field
        'account_id',
        'name',
        'helper_table',
        'balance_type',
        'report_type',
        'debit',
        'credit'
    ];

    protected $casts = [
        'balance_type' => 'string',
        'report_type' => 'string',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2'
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
        return $this->hasMany(JurnalUmum::class, 'account_id', 'account_id');
    }
}