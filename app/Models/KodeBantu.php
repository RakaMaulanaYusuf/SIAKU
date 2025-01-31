<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KodeBantu extends Model
{
   protected $table = 'kode_bantu';
   
   protected $fillable = [
       'company_id',
       'helper_id',      // Diubah dari 'code' ke 'helper_id'
       'name',
       'status',
       'balance'
   ];

   // Menambahkan casting untuk enum dan decimal
   protected $casts = [
       'status' => 'string',    // PIUTANG atau HUTANG 
       'balance' => 'decimal:2'
   ];

   public function company()
   {
       return $this->belongsTo(Company::class);
   }

   public function journalEntries()
   {
        return $this->hasMany(JurnalUmum::class, 'helper_id', 'helper_id');
   }
}