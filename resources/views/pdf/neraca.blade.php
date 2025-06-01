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
        
        .date {
            font-size: 12px;
            margin-bottom: 15px;
        }
        
        .balance-sheet {
            display: table;
            width: 100%;
        }
        
        .left-side, .right-side {
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
        }
        
        .account-name {
            flex: 1;
            padding-left: 15px;
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
        }
        
        .total {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            font-weight: bold;
            font-size: 12px;
            margin: 15px 0;
            padding: 8px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .line-item {
            border-bottom: 1px dotted #ccc;
            padding: 2px 0;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-left {
            text-align: left;
        }
        
        .text-center {
            text-align: center;
        }
        
        .bold {
            font-weight: bold;
        }
        
        .double-underline {
            border-bottom: 3px double #000;
        }
        
        .balance-container {
            display: flex;
            width: 100%;
        }
        
        .assets-side {
            width: 50%;
            padding-right: 20px;
        }
        
        .liabilities-side {
            width: 50%;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="company-name">{{ strtoupper($companyName) }}</div>
        <div class="title">{{ $title }}</div>
        <div class="date">Per {{ $date }}</div>
    </div>

    {{-- Balance Sheet Content --}}
    <div class="balance-container">
        {{-- AKTIVA --}}
        <div class="assets-side">
            <div class="section-title">AKTIVA</div>
            
            {{-- Aktiva Lancar --}}
            <div class="subsection-title">AKTIVA LANCAR:</div>
            <table>
                @php
                    $aktivaLancar = $aktivaAccounts->filter(function($account) {
                        return strpos(strtolower($account->name), 'kas') !== false || 
                               strpos(strtolower($account->name), 'bank') !== false ||
                               strpos(strtolower($account->name), 'piutang') !== false ||
                               strpos(strtolower($account->name), 'persediaan') !== false ||
                               strpos(strtolower($account->name), 'perlengkapan') !== false;
                    });
                    $totalAktivaLancar = $aktivaLancar->sum('balance');
                @endphp
                
                @foreach($aktivaLancar as $account)
                @if($account->balance > 0)
                <tr class="line-item">
                    <td class="text-left" style="padding-left: 15px;">{{ $account->name }}</td>
                    <td class="text-right" style="width: 100px;">{{ number_format($account->balance, 0, ',', '.') }}</td>
                </tr>
                @endif
                @endforeach
                <tr class="subtotal">
                    <td class="text-left bold">Total Aktiva Lancar</td>
                    <td class="text-right bold">{{ number_format($totalAktivaLancar, 0, ',', '.') }}</td>
                </tr>
            </table>
            
            {{-- Aktiva Tetap --}}
            <div class="subsection-title">AKTIVA TETAP:</div>
            <table>
                @php
                    $aktivaTetap = $aktivaAccounts->filter(function($account) {
                        return strpos(strtolower($account->name), 'gedung') !== false || 
                               strpos(strtolower($account->name), 'kendaraan') !== false ||
                               strpos(strtolower($account->name), 'mesin') !== false ||
                               strpos(strtolower($account->name), 'peralatan') !== false ||
                               strpos(strtolower($account->name), 'akumulasi') === false;
                    });
                    $totalAktivaTetap = $aktivaTetap->sum('balance');
                @endphp
                
                @foreach($aktivaTetap as $account)
                @if($account->balance > 0)
                <tr class="line-item">
                    <td class="text-left" style="padding-left: 15px;">{{ $account->name }}</td>
                    <td class="text-right" style="width: 100px;">{{ number_format($account->balance, 0, ',', '.') }}</td>
                </tr>
                @endif
                @endforeach
                <tr class="subtotal">
                    <td class="text-left bold">Total Aktiva Tetap</td>
                    <td class="text-right bold">{{ number_format($totalAktivaTetap, 0, ',', '.') }}</td>
                </tr>
            </table>
            
            {{-- Total Aktiva --}}
            <table>
                <tr class="total">
                    <td class="text-left bold">TOTAL AKTIVA</td>
                    <td class="text-right bold double-underline" style="width: 100px;">{{ number_format($totalAktiva, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        {{-- PASSIVA --}}
        <div class="liabilities-side">
            <div class="section-title">PASSIVA</div>
            
            {{-- Hutang --}}
            <div class="subsection-title">HUTANG:</div>
            <table>
                @php
                    $hutang = $passivaAccounts->filter(function($account) {
                        return strpos(strtolower($account->name), 'hutang') !== false ||
                               strpos(strtolower($account->name), 'utang') !== false;
                    });
                    $totalHutang = $hutang->sum('balance');
                @endphp
                
                @foreach($hutang as $account)
                @if($account->balance > 0)
                <tr class="line-item">
                    <td class="text-left" style="padding-left: 15px;">{{ $account->name }}</td>
                    <td class="text-right" style="width: 100px;">{{ number_format($account->balance, 0, ',', '.') }}</td>
                </tr>
                @endif
                @endforeach
                <tr class="subtotal">
                    <td class="text-left bold">Total Hutang</td>
                    <td class="text-right bold">{{ number_format($totalHutang, 0, ',', '.') }}</td>
                </tr>
            </table>
            
            {{-- Modal --}}
            <div class="subsection-title">MODAL:</div>
            <table>
                @php
                    $modal = $passivaAccounts->filter(function($account) {
                        return strpos(strtolower($account->name), 'modal') !== false ||
                               strpos(strtolower($account->name), 'ekuitas') !== false;
                    });
                    $totalModal = $modal->sum('balance');
                @endphp
                
                @foreach($modal as $account)
                @if($account->balance > 0)
                <tr class="line-item">
                    <td class="text-left" style="padding-left: 15px;">{{ $account->name }}</td>
                    <td class="text-right" style="width: 100px;">{{ number_format($account->balance, 0, ',', '.') }}</td>
                </tr>
                @endif
                @endforeach
                <tr class="subtotal">
                    <td class="text-left bold">Total Modal</td>
                    <td class="text-right bold">{{ number_format($totalModal, 0, ',', '.') }}</td>
                </tr>
            </table>
            
            {{-- Total Passiva --}}
            <table>
                <tr class="total">
                    <td class="text-left bold">TOTAL PASSIVA</td>
                    <td class="text-right bold double-underline" style="width: 100px;">{{ number_format($totalPassiva, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Footer --}}
    <div style="margin-top: 50px; text-align: right; font-size: 10px;">
        <p>Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
    </div>
</body>
</html>