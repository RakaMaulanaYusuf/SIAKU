<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KodeAkun extends Model
{
    protected $table = 'kode_akun';
    
    protected $fillable = [
        'company_id',
        'account_id',       // Diubah dari 'code' ke 'account_id'
        'name',
        'helper_table',     // Diubah dari 'table' ke 'helper_table'
        'balance_type',
        'report_type',
        'debit',
        'credit'
    ];

    // Opsional: Menambahkan casting untuk enum
    protected $casts = [
        'balance_type' => 'string',   // DEBIT atau CREDIT
        'report_type' => 'string',    // NERACA atau LABARUGI
        'debit' => 'decimal:2',
        'credit' => 'decimal:2'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Tambahkan relationship untuk jurnal umum
    public function journalEntries()
    {
        return $this->hasMany(JurnalUmum::class, 'account_id', 'account_id');
    }
}