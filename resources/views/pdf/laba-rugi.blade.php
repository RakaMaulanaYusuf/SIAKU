{{-- resources/views/pdf/laba-rugi.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .period {
            font-size: 12px;
            margin-bottom: 5px; /* Sesuaikan agar tidak terlalu jauh */
        }

        .date-print { /* Tambahkan style untuk tanggal cetak */
            font-size: 10px;
            margin-bottom: 15px;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        
        .account-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 2px 0;
        }
        
        .account-name {
            flex: 1;
            padding-left: 20px;
            text-align: left; /* Pastikan teks nama rata kiri */
        }
        
        .account-amount {
            width: 120px;
            text-align: right;
            padding-right: 20px;
        }
        
        .subtotal {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            font-weight: bold;
            margin: 10px 0;
            padding: 5px 0;
            display: flex; /* Gunakan flex untuk baris subtotal */
            justify-content: space-between;
        }
        .subtotal .label {
            flex: 1;
            padding-left: 20px;
            text-align: left;
        }
        .subtotal .amount {
            width: 120px;
            text-align: right;
            padding-right: 20px;
        }
        
        .total {
            border-top: 3px double #000;
            border-bottom: 3px double #000;
            font-weight: bold;
            font-size: 12px;
            margin: 15px 0;
            padding: 8px 0;
            display: flex; /* Gunakan flex untuk baris total */
            justify-content: space-between;
        }
        .total .label {
            flex: 1;
            padding-left: 20px;
            text-align: left;
        }
        .total .amount {
            width: 120px;
            text-align: right;
            padding-right: 20px;
        }
        
        .main-section {
            margin-bottom: 25px;
        }
        
        /* Menggunakan DIVs daripada TABLE untuk struktur yang lebih fleksibel seperti di contoh HTML */
        .report-table {
            width: 100%;
            /* border-collapse: collapse; */ /* Tidak perlu jika pakai divs */
        }
        
        .report-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            border-bottom: 1px dotted #ccc; /* Untuk garis putus-putus */
        }
        
        .report-cell-label {
            flex: 1;
            padding-left: 20px;
            text-align: left;
        }
        
        .report-cell-amount {
            width: 120px;
            text-align: right;
            padding-right: 20px;
        }
        
        .bold {
            font-weight: bold;
        }
        
        .underline {
            text-decoration: underline;
        }
        
        /* double-underline akan diaplikasikan via border-bottom di .total */
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="company-name">{{ strtoupper($companyName) }}</div>
        <div class="title">{{ $title }}</div>
        <div class="period">Untuk Periode yang Berakhir {{ $periodName }}</div> {{-- Menggunakan $periodName --}}
        <div class="date-print">Tanggal Cetak: {{ strtoupper($date) }}</div>
    </div>

    {{-- PENDAPATAN --}}
    <div class="main-section">
        <div class="section-title">PENDAPATAN:</div>
        <div class="report-table">
            @foreach($pendapatan as $account) {{-- Menggunakan $pendapatan --}}
            @if($account['amount'] > 0) {{-- Mengakses dengan array key 'amount' --}}
            <div class="report-row">
                <div class="report-cell-label">{{ $account['name'] }}</div>
                <div class="report-cell-amount">Rp {{ number_format($account['amount'], 0, ',', '.') }}</div>
            </div>
            @endif
            @endforeach
            <div class="subtotal">
                <div class="label">TOTAL PENDAPATAN</div>
                <div class="amount">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- HARGA POKOK PENJUALAN (HPP) --}}
    <div class="main-section">
        <div class="section-title">HARGA POKOK PENJUALAN:</div>
        <div class="report-table">
            @foreach($hpp as $account) {{-- Menggunakan $hpp --}}
            @if($account['amount'] > 0) {{-- Mengakses dengan array key 'amount' --}}
            <div class="report-row">
                <div class="report-cell-label">{{ $account['name'] }}</div>
                <div class="report-cell-amount">Rp {{ number_format($account['amount'], 0, ',', '.') }}</div>
            </div>
            @endif
            @endforeach
            <div class="subtotal">
                <div class="label">TOTAL HARGA POKOK PENJUALAN</div>
                <div class="amount">Rp {{ number_format($totalHPP, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- LABA KOTOR --}}
    <div class="main-section">
        <div class="total"> {{-- Menggunakan class total untuk laba kotor --}}
            <div class="label">LABA KOTOR</div>
            <div class="amount">Rp {{ number_format($labaKotor, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- BEBAN OPERASIONAL --}}
    <div class="main-section">
        <div class="section-title">BEBAN OPERASIONAL:</div>
        <div class="report-table">
            @foreach($biaya as $account) {{-- Menggunakan $biaya --}}
            @if($account['amount'] > 0) {{-- Mengakses dengan array key 'amount' --}}
            <div class="report-row">
                <div class="report-cell-label">{{ $account['name'] }}</div>
                <div class="report-cell-amount">Rp {{ number_format($account['amount'], 0, ',', '.') }}</div>
            </div>
            @endif
            @endforeach
            <div class="subtotal">
                <div class="label">TOTAL BEBAN OPERASIONAL</div>
                <div class="amount">Rp {{ number_format($totalBiayaOperasional, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- LABA/RUGI BERSIH --}}
    <div class="main-section">
        <div class="total"> {{-- Menggunakan class total --}}
            <div class="label">
                @if($labaBersih >= 0) {{-- Menggunakan $labaBersih --}}
                    LABA BERSIH
                @else
                    RUGI BERSIH
                @endif
            </div>
            <div class="amount double-underline"> {{-- double-underline diterapkan di sini --}}
                Rp {{ number_format(abs($labaBersih), 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div style="margin-top: 50px; text-align: right; font-size: 10px;">
        <p>Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
    </div>
</body>
</html>