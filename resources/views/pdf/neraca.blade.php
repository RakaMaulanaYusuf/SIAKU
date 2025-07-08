{{-- resources/views/pdf/neraca.blade.php --}}
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
        
        .period-date { /* Kombinasi periode dan tanggal cetak */
            font-size: 12px;
            margin-bottom: 15px;
        }
        
        .balance-sheet {
            display: table; /* Menggunakan table-layout untuk 2 kolom */
            width: 100%;
        }
        
        .balance-sheet-row {
            display: table-row;
        }

        .assets-side, .liabilities-side {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
            text-decoration: underline;
            text-align: center;
        }
        
        .subsection-title {
            font-weight: bold;
            font-size: 11px;
            margin: 15px 0 8px 0;
            text-decoration: underline;
        }
        
        .account-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            padding: 2px 0;
            border-bottom: 1px dotted #ccc; /* Garis putus-putus */
        }
        
        .account-name {
            flex: 1;
            padding-left: 15px;
            text-align: left;
        }
        
        .account-amount {
            width: 100px;
            text-align: right;
        }
        
        .subtotal {
            border-top: 1px solid #000;
            font-weight: bold;
            margin: 8px 0;
            padding: 5px 0;
            display: flex;
            justify-content: space-between;
        }
        .subtotal .label {
            flex: 1;
            padding-left: 15px;
            text-align: left;
        }
        .subtotal .amount {
            width: 100px;
            text-align: right;
        }
        
        .total {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            font-weight: bold;
            font-size: 12px;
            margin: 15px 0;
            padding: 8px 0;
            display: flex;
            justify-content: space-between;
        }
        .total .label {
            flex: 1;
            padding-left: 15px;
            text-align: left;
        }
        .total .amount {
            width: 100px;
            text-align: right;
        }
        
        .final-total {
            border-top: 3px double #000; /* Garis ganda untuk total akhir */
            font-weight: bold;
            font-size: 12px;
            margin-top: 20px;
            padding: 8px 0;
            display: flex;
            justify-content: space-between;
        }
        .final-total .label {
            flex: 1;
            padding-left: 15px;
            text-align: left;
        }
        .final-total .amount {
            width: 100px;
            text-align: right;
        }

        .laba-ditahan {
            margin: 15px 0;
            font-weight: bold;
            padding-left: 15px;
            text-align: left;
            display: flex;
            justify-content: space-between;
            align-items: baseline;
        }
        .laba-ditahan .label {
            flex: 1;
            padding-left: 15px;
            text-align: left;
        }
        .laba-ditahan .amount {
            width: 100px;
            text-align: right;
        }

        /* Footer Cetak */
        .print-footer {
            margin-top: 50px; 
            text-align: right; 
            font-size: 10px;
        }

    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="company-name">{{ strtoupper($companyName) }}</div>
        <div class="title">{{ $title }}</div>
        <div class="period-date">Per {{ $periodName }}</div> {{-- Menggunakan $periodName --}}
    </div>

    {{-- Balance Sheet Content --}}
    <div class="balance-sheet">
        {{-- AKTIVA --}}
        <div class="assets-side">
            <div class="section-title">AKTIVA</div>
            
            {{-- Aktiva Lancar --}}
            <div class="subsection-title">AKTIVA LANCAR:</div>
            <div>
                @foreach($aktivaLancar as $account) {{-- Langsung looping $aktivaLancar --}}
                @if($account['amount'] > 0) {{-- Akses amount --}}
                <div class="account-line">
                    <div class="account-name">{{ $account['name'] }}</div>
                    <div class="account-amount">{{ number_format($account['amount'], 0, ',', '.') }}</div>
                </div>
                @endif
                @endforeach
                <div class="subtotal">
                    <div class="label">Total Aktiva Lancar</div>
                    <div class="amount">{{ number_format($totalAktivaLancar, 0, ',', '.') }}</div>
                </div>
            </div>
            
            {{-- Aktiva Tetap --}}
            <div class="subsection-title">AKTIVA TETAP:</div>
            <div>
                @foreach($aktivaTetap as $account) {{-- Langsung looping $aktivaTetap --}}
                @if($account['amount'] > 0) {{-- Akses amount --}}
                <div class="account-line">
                    <div class="account-name">{{ $account['name'] }}</div>
                    <div class="account-amount">{{ number_format($account['amount'], 0, ',', '.') }}</div>
                </div>
                @endif
                @endforeach
                <div class="subtotal">
                    <div class="label">Total Aktiva Tetap</div>
                    <div class="amount">{{ number_format($totalAktivaTetap, 0, ',', '.') }}</div>
                </div>
            </div>
            
            {{-- Total Aktiva --}}
            <div class="total">
                <div class="label">TOTAL AKTIVA</div>
                <div class="amount">{{ number_format($totalAktiva, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- KEWAJIBAN & EKUITAS (PASSIVA) --}}
        <div class="liabilities-side">
            <div class="section-title">KEWAJIBAN & EKUITAS</div>
            
            {{-- Kewajiban --}}
            <div class="subsection-title">KEWAJIBAN:</div>
            <div>
                @foreach($kewajiban as $account) {{-- Langsung looping $kewajiban --}}
                @if($account['amount'] > 0) {{-- Akses amount --}}
                <div class="account-line">
                    <div class="account-name">{{ $account['name'] }}</div>
                    <div class="account-amount">{{ number_format($account['amount'], 0, ',', '.') }}</div>
                </div>
                @endif
                @endforeach
                <div class="subtotal">
                    <div class="label">Total Kewajiban</div>
                    <div class="amount">{{ number_format($totalKewajiban, 0, ',', '.') }}</div>
                </div>
            </div>
            
            {{-- Ekuitas --}}
            <div class="subsection-title">EKUITAS:</div>
            <div>
                @foreach($ekuitas as $account) {{-- Langsung looping $ekuitas --}}
                @if($account['amount'] > 0) {{-- Akses amount --}}
                <div class="account-line">
                    <div class="account-name">{{ $account['name'] }}</div>
                    <div class="account-amount">{{ number_format($account['amount'], 0, ',', '.') }}</div>
                </div>
                @endif
                @endforeach
                {{-- Laba Bersih Tahun Berjalan (sebagai bagian dari Ekuitas) --}}
                @if(isset($labaBersihTahunBerjalan))
                <div class="account-line laba-ditahan">
                    <div class="account-name">Laba Bersih Tahun Berjalan</div>
                    <div class="account-amount">{{ number_format($labaBersihTahunBerjalan, 0, ',', '.') }}</div>
                </div>
                @endif
                <div class="subtotal">
                    <div class="label">Total Ekuitas</div>
                    <div class="amount">{{ number_format($totalEkuitas + $labaBersihTahunBerjalan, 0, ',', '.') }}</div> {{-- Tambahkan laba ditahan ke total ekuitas --}}
                </div>
            </div>
            
            {{-- Total Kewajiban & Ekuitas --}}
            <div class="total">
                <div class="label">TOTAL KEWAJIBAN & EKUITAS</div>
                <div class="amount">{{ number_format($totalKewajibanDanEkuitas + $labaBersihTahunBerjalan, 0, ',', '.') }}</div> {{-- Tambahkan laba ditahan --}}
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="print-footer">
        <p>Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
    </div>
</body>
</html>