<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BukuBesar extends Model
{
    protected $table = 'jurnal_umum';
    protected $guarded = [];
    
    public function kodeAkun()
    {
        return $this->belongsTo(KodeAkun::class, 'account_id', 'account_id');
    }
}