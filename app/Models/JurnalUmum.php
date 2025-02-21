<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class JurnalUmum extends Model
{
    use HasFactory;

    protected $table = 'jurnal_umum';

    protected $fillable = [
        'company_id',
        'company_period_id',
        'date',
        'transaction_proof',
        'description',
        'account_id',
        'helper_id',
        'debit',
        'credit'
    ];

    protected $casts = [
        'date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    protected $attributes = [
        'debit' => null,
        'credit' => null,
    ];

    protected $rules = [
        'date' => 'required|date',
        'transaction_proof' => 'required|string',
        'description' => 'required|string',
        'account_id' => 'required|string|exists:kode_akun,account_id',
        'helper_id' => 'nullable|string|exists:kode_bantu,helper_id',
        'debit' => 'required_without:credit|nullable|numeric|min:0',
        'credit' => 'required_without:debit|nullable|numeric|min:0',
    ];

    public function account()
    {
        return $this->belongsTo(KodeAkun::class, 'account_id', 'account_id');
    }

    public function helper()
    {
        return $this->belongsTo(KodeBantu::class, 'helper_id', 'helper_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function period()
    {
        return $this->belongsTo(CompanyPeriod::class, 'company_period_id');
    }

    protected function validateData($data)
    {
        $validator = Validator::make($data, $this->rules);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return true;
    }
}