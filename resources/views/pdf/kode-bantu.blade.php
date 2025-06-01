{{-- resources/views/pdf/kode-bantu.blade.php --}}
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
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        .text-center {
            text-align: center;
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
        
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
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
        <div class="date">Per {{ $date }}</div>
    </div>

    {{-- Table --}}
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">NO</th>
                <th style="width: 20%;">KODE BANTU</th>
                <th style="width: 40%;">NAMA</th>
                <th style="width: 15%;">STATUS</th>
                <th style="width: 15%;">SALDO AWAL</th>
            </tr>
        </thead>
        <tbody>
            @if($kodeBantu->count() > 0)
                @foreach($kodeBantu as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->helper_id }}</td>
                    <td>{{ $item->name }}</td>
                    <td class="text-center">{{ $item->status }}</td>
                    <td class="text-right">{{ number_format($item->balance ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                
                {{-- Total Row --}}
                <tr class="total-row">
                    <td colspan="4" class="text-center bold">TOTAL SALDO AWAL</td>
                    <td class="text-right bold">{{ number_format($totalBalance, 0, ',', '.') }}</td>
                </tr>
                
                {{-- Summary Row --}}
                <tr class="total-row">
                    <td colspan="4" class="text-center bold">TOTAL RECORDS</td>
                    <td class="text-center bold">{{ $totalRecords }} item(s)</td>
                </tr>
            @else
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data kode bantu</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
    </div>
</body>
</html>