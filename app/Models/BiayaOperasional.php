<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiayaOperasional extends Model
{
    protected $table = 'biaya_operasional';
    
    protected $fillable = [
        'company_id',
        'account_id',
        'name',
        'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the company that owns the BiayaOperasional
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the account associated with the BiayaOperasional
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class, 'account_id', 'account_id');
    }

    /**
     * Scope to get total biaya operasional for a company
     */
    public function scopeTotalForCompany($query, $company_id)
    {
        return $query->where('company_id', $company_id)
                    ->sum('amount');
    }
}