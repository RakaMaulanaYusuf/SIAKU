<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AktivaTetap extends Model
{
    protected $table = 'aktiva_tetap';
    
    protected $fillable = [
        'company_id',
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

    public function account(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class, 'account_id', 'account_id');
    }

    public function scopeTotalForCompany($query, $company_id)
    {
        return $query->where('company_id', $company_id)
                    ->sum('amount');
    }
}