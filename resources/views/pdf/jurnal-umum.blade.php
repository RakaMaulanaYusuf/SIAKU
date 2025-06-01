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
        
        .col-tanggal {
            width: 8%;
        }
        
        .col-bukti {
            width: 8%;
        }
        
        .col-keterangan {
            width: 30%;
        }
        
        .col-kode-akun {
            width: 8%;
        }
        
        .col-kode-bantu {
            width: 8%;
        }
        
        .col-debet {
            width: 12%;
        }
        
        .col-kredit {
            width: 12%;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="company-name">{{ strtoupper($companyName) }}</div>
        <div class="title">{{ $title }}</div>
        <div class="date">{{ strtoupper($date) }}</div>
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
            @foreach($jurnals as $jurnal)
            <tr>
                <td class="text-center">{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d-m-y') }}</td>
                <td class="text-center">{{ $jurnal->nomor_jurnal }}</td>
                <td>{{ $jurnal->keterangan }}</td>
                <td class="text-center">{{ $jurnal->kodeAkun->account_id ?? '-' }}</td>
                <td class="text-center">{{ $jurnal->kodeBantu->kode_bantu ?? '-' }}</td>
                <td class="text-right">
                    @if($jurnal->debit > 0)
                        {{ number_format($jurnal->debit, 0, ',', '.') }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">
                    @if($jurnal->kredit > 0)
                        {{ number_format($jurnal->kredit, 0, ',', '.') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach

            {{-- Total Row --}}
            <tr class="total-row">
                <td colspan="5" class="text-center"><strong>TOTAL</strong></td>
                <td class="text-right">
                    <strong>{{ number_format($totalDebit, 0, ',', '.') }}</strong>
                </td>
                <td class="text-right">
                    <strong>{{ number_format($totalCredit, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
