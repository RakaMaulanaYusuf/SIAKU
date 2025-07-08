<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyPeriodSeeder extends Seeder 
{
   public function run()
   {
       DB::table('company_period')->insert([
           [
               'company_id' => 1,
               'period_month' => 'Juli',
               'period_year' => 2025,
               'created_at' => now(),
               'updated_at' => now()
           ],
           [
               'company_id' => 2,
               'period_month' => 'Januari', 
               'period_year' => 2025,
               'created_at' => now(),
               'updated_at' => now()
           ],
           [
               'company_id' => 2,
               'period_month' => 'Februari',
               'period_year' => 2025,
               'created_at' => now(),
               'updated_at' => now() 
           ],
       ]);
   }
}