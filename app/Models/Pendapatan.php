<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pendapatan extends Model
{
    protected $table = 'pendapatan';
    
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
     * Get the company that owns the Pendapatan
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the account associated with the Pendapatan
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class, 'account_id', 'account_id');
    }
}