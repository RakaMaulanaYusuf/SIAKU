<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyPeriod extends Model
{
    protected $table = 'company_period';
    
    protected $fillable = [
        'company_id',
        'period_month',
        'period_year'
    ];

    protected $casts = [
        'period_year' => 'integer'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'company_period_id');
    }

    // Relasi untuk users yang di-assign ke period ini
    public function assignedUsers()
    {
        return $this->hasMany(User::class, 'assigned_company_period_id');
    }

    // Accessor untuk nama periode yang lengkap
    public function getPeriodNameAttribute()
    {
        return $this->period_month . ' ' . $this->period_year;
    }

    // Accessor untuk format periode yang mudah dibaca
    public function getFormattedPeriodAttribute()
    {
        return $this->period_month . ' ' . $this->period_year;
    }

    // Method untuk mengecek apakah periode sedang berlangsung
    public function isCurrentPeriod()
    {
        $now = now();
        $currentMonth = $now->format('F'); // Full month name in English
        $currentYear = $now->year;
        
        // Konversi nama bulan Indonesia ke Inggris untuk perbandingan
        $monthMapping = [
            'Januari' => 'January',
            'Februari' => 'February', 
            'Maret' => 'March',
            'April' => 'April',
            'Mei' => 'May',
            'Juni' => 'June',
            'Juli' => 'July',
            'Agustus' => 'August',
            'September' => 'September',
            'Oktober' => 'October',
            'November' => 'November',
            'Desember' => 'December'
        ];

        $englishMonth = $monthMapping[$this->period_month] ?? $this->period_month;
        
        return $englishMonth === $currentMonth && $this->period_year == $currentYear;
    }

    // Scope untuk periode tahun tertentu
    public function scopeForYear($query, $year)
    {
        return $query->where('period_year', $year);
    }

    // Scope untuk periode bulan tertentu
    public function scopeForMonth($query, $month)
    {
        return $query->where('period_month', $month);
    }

    // Method untuk mendapatkan periode sebelumnya
    public function getPreviousPeriod()
    {
        $months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $currentIndex = array_search($this->period_month, $months);
        
        if ($currentIndex === 0) {
            // Jika Januari, maka periode sebelumnya adalah Desember tahun sebelumnya
            $prevMonth = 'Desember';
            $prevYear = $this->period_year - 1;
        } else {
            $prevMonth = $months[$currentIndex - 1];
            $prevYear = $this->period_year;
        }
        
        return self::where('company_id', $this->company_id)
            ->where('period_month', $prevMonth)
            ->where('period_year', $prevYear)
            ->first();
    }

    // Method untuk mendapatkan periode berikutnya
    public function getNextPeriod()
    {
        $months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $currentIndex = array_search($this->period_month, $months);
        
        if ($currentIndex === 11) {
            // Jika Desember, maka periode berikutnya adalah Januari tahun berikutnya
            $nextMonth = 'Januari';
            $nextYear = $this->period_year + 1;
        } else {
            $nextMonth = $months[$currentIndex + 1];
            $nextYear = $this->period_year;
        }
        
        return self::where('company_id', $this->company_id)
            ->where('period_month', $nextMonth)
            ->where('period_year', $nextYear)
            ->first();
    }
}