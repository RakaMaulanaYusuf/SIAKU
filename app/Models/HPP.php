<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HPP extends Model
{
    protected $table = 'hpp';
    
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
     * Get the company that owns the HPP
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the account associated with the HPP
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class, 'account_id', 'account_id');
    }

    /**
     * Scope to get total HPP for a company
     */
    public function scopeTotalForCompany($query, $company_id)
    {
        return $query->where('company_id', $company_id)
                    ->sum('amount');
    }
}