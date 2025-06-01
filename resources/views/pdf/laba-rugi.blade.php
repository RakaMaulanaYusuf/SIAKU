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
        }
        
        .total {
            border-top: 3px double #000;
            border-bottom: 3px double #000;
            font-weight: bold;
            font-size: 12px;
            margin: 15px 0;
            padding: 8px 0;
        }
        
        .main-section {
            margin-bottom: 25px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .line-item {
            border-bottom: 1px dotted #ccc;
            padding: 3px 0;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-left {
            text-align: left;
        }
        
        .bold {
            font-weight: bold;
        }
        
        .underline {
            text-decoration: underline;
        }
        
        .double-underline {
            border-bottom: 3px double #000;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="company-name">{{ strtoupper($companyName) }}</div>
        <div class="title">{{ $title }}</div>
        <div class="period">Untuk Periode yang Berakhir {{ $period }}</div>
    </div>

    {{-- PENDAPATAN --}}
    <div class="main-section">
        <div class="section-title">PENDAPATAN:</div>
        <table>
            @foreach($pendapatanAccounts as $account)
            @if($account->balance > 0)
            <tr class="line-item">
                <td class="text-left" style="padding-left: 20px;">{{ $account->name }}</td>
                <td class="text-right" style="width: 120px;">Rp {{ number_format($account->balance, 0, ',', '.') }}</td>
            </tr>
            @endif
            @endforeach
            <tr class="subtotal">
                <td class="text-left bold">TOTAL PENDAPATAN</td>
                <td class="text-right bold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    {{-- BEBAN OPERASIONAL --}}
    <div class="main-section">
        <div class="section-title">BEBAN OPERASIONAL:</div>
        <table>
            @foreach($bebanAccounts as $account)
            @if($account->balance > 0)
            <tr class="line-item">
                <td class="text-left" style="padding-left: 20px;">{{ $account->name }}</td>
                <td class="text-right" style="width: 120px;">Rp {{ number_format($account->balance, 0, ',', '.') }}</td>
            </tr>
            @endif
            @endforeach
            <tr class="subtotal">
                <td class="text-left bold">TOTAL BEBAN OPERASIONAL</td>
                <td class="text-right bold">Rp {{ number_format($totalBeban, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    {{-- LABA/RUGI BERSIH --}}
    <div class="main-section">
        <table>
            <tr class="total">
                <td class="text-left bold">
                    @if($labaRugi >= 0)
                        LABA BERSIH
                    @else
                        RUGI BERSIH
                    @endif
                </td>
                <td class="text-right bold double-underline" style="width: 120px;">
                    Rp {{ number_format(abs($labaRugi), 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Footer --}}
    <div style="margin-top: 50px; text-align: right; font-size: 10px;">
        <p>Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
    </div>
</body>
</html>