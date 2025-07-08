<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompaniesSeeder extends Seeder
{
   public function run()
   {
       DB::table('companies')->insert([
           [
               'name' => 'PT MAJU MUNDUR',
               'type' => 'Perdagangan Umum',
               'address' => 'Jl. Maju No. 123, Jakarta',
               'phone' => '021-5551234',
               'email' => 'majumundur@gmail.com',
            //    'status' => 'Aktif',
               'created_at' => now(),
               'updated_at' => now(),
           ],
           [
               'name' => 'PT JAYA ABADI',
               'type' => 'Perdagangan',
               'address' => 'Jl. Jaya No. 456, Jakarta', 
               'phone' => '031-5555678',
               'email' => 'jayaabadi@gmail.com',
            //    'status' => 'Nonaktif',
               'created_at' => now(),
               'updated_at' => now(),
           ],
       ]);
   }
}