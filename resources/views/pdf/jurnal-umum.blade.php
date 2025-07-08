{{-- resources/views/pdf/jurnal-umum.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .date {
            font-size: 10px;
            margin-bottom: 15px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 3px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 8px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        
        /* Menggunakan % untuk lebar kolom agar lebih adaptif */
        .col-tanggal {
            width: 10%; /* Disesuaikan */
        }
        
        .col-bukti {
            width: 10%; /* Disesuaikan */
        }
        
        .col-keterangan {
            width: 35%; /* Disesuaikan, karena 2 kolom nama dihilangkan */
        }
        
        .col-kode-akun {
            width: 10%; /* Disesuaikan */
        }
        
        .col-kode-bantu {
            width: 10%; /* Disesuaikan */
        }
        
        .col-debet {
            width: 12.5%; /* Disesuaikan */
        }
        
        .col-kredit {
            width: 12.5%; /* Disesuaikan */
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="company-name">{{ strtoupper($companyName) }}</div>
        <div class="title">{{ $title }}</div>
        <div class="date">Per {{ strtoupper($date) }}</div>
        @if(isset($periodName))
            <div class="date">Periode: {{ $periodName }}</div>
        @endif
    </div>

    {{-- Table --}}
    <table>
        <thead>
            <tr>
                <th class="col-tanggal">TANGGAL</th>
                <th class="col-bukti">BUKTI<br>TRANSAKSI</th>
                <th class="col-keterangan">KETERANGAN</th>
                <th class="col-kode-akun">KODE<br>AKUN</th>
                <th class="col-kode-bantu">KODE<br>BANTU</th>
                <th class="col-debet">DEBET</th>
                <th class="col-kredit">KREDIT</th>
            </tr>
        </thead>
        <tbody>
            {{-- Data Jurnal --}}
            @foreach($journals as $jurnal)
            <tr>
                <td class="text-center">{{ $jurnal['date'] }}</td>
                <td class="text-center">{{ $jurnal['transaction_proof'] }}</td>
                <td>{{ $jurnal['description'] }}</td>
                <td class="text-center">{{ $jurnal['account_id'] }}</td> {{-- Hanya Kode Akun --}}
                <td class="text-center">{{ $jurnal['helper_id'] ?? '-' }}</td> {{-- Hanya Kode Bantu --}}
                <td class="text-right">
                    @if(isset($jurnal['debit']) && $jurnal['debit'] > 0)
                        {{ number_format($jurnal['debit'], 0, ',', '.') }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">
                    @if(isset($jurnal['credit']) && $jurnal['credit'] > 0)
                        {{ number_format($jurnal['credit'], 0, ',', '.') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach

            {{-- Total Row --}}
            <tr class="total-row">
                <td colspan="5" class="text-center"><strong>TOTAL</strong></td> {{-- colspan disesuaikan: 7 - 2 = 5 --}}
                <td class="text-right">
                    <strong>{{ number_format($totalDebit, 0, ',', '.') }}</strong>
                </td>
                <td class="text-right">
                    <strong>{{ number_format($totalCredit, 0, ',', '.') }}</strong>
                </td>
            </tr>
            {{-- Status Balance --}}
            <tr>
                <td colspan="7" class="text-center" style="padding-top: 10px;"> {{-- colspan disesuaikan: 9 - 2 = 7 --}}
                    Status Balance: {{ $isBalanced ? 'BALANCE' : 'TIDAK BALANCE' }}
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>