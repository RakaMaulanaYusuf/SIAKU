<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pendapatan extends Model
{
    protected $table = 'pendapatan';
    
    protected $fillable = [
        'company_id',
        'company_period_id',
        'account_id',
        'name',
        'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(CompanyPeriod::class, 'company_period_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class, 'account_id', 'account_id');
    }
}