{{-- resources/views/pdf/buku-besar-pembantu.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
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
        
        .account-info {
            margin-bottom: 15px;
        }
        
        .account-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .account-table td {
            border: 1px solid #000;
            padding: 5px;
            font-weight: bold;
        }
        
        .account-name {
            width: 50%;
        }
        
        .account-code {
            width: 25%;
            text-align: center;
        }
        
        .account-type {
            width: 25%;
            text-align: center;
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
            font-size: 9px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .col-no {
            width: 5%;
        }
        
        .col-tanggal {
            width: 12%;
        }
        
        .col-bukti {
            width: 12%;
        }
        
        .col-keterangan {
            width: 35%;
        }
        
        .col-debet {
            width: 12%;
        }
        
        .col-kredit {
            width: 12%;
        }
        
        .col-saldo {
            width: 12%;
        }
        
        .empty-row {
            height: 20px;
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

    {{-- Account Info --}}
    <div class="account-info">
        <table class="account-table">
            <tr>
                <td class="account-name">NAMA: {{ $kodeBantu->nama_kode_bantu }}</td>
                <td class="account-code">{{ $kodeBantu->kode_bantu }}</td>
                <td class="account-type">{{ $kodeBantu->status ?? 'HUTANG' }}</td>
            </tr>
        </table>
    </div>

    {{-- Transactions Table --}}
    <table>
        <thead>
            <tr>
                <th class="col-no">NO</th>
                <th class="col-tanggal">TANGGAL</th>
                <th class="col-bukti">BUKTI<br>TRANSAKSI</th>
                <th class="col-keterangan">KETERANGAN</th>
                <th class="col-debet">DEBET</th>
                <th class="col-kredit">KREDIT</th>
                <th class="col-saldo">SALDO</th>
            </tr>
        </thead>
        <tbody>
            {{-- Saldo Awal --}}
            <tr>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td>Saldo awal</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
            </tr>

            {{-- Data Transactions --}}
            @foreach($transactions as $index => $transaction)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($transaction->tanggal)->format('d-m-y') }}</td>
                <td class="text-center">{{ $transaction->nomor_jurnal }}</td>
                <td>{{ $transaction->keterangan }}</td>
                <td class="text-right">
                    @if($transaction->debit > 0)
                        {{ number_format($transaction->debit, 0, ',', '.') }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">
                    @if($transaction->kredit > 0)
                        {{ number_format($transaction->kredit, 0, ',', '.') }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">{{ number_format($transaction->running_balance, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            {{-- Empty rows untuk penambahan manual --}}
            @for($i = 0; $i < 10; $i++)
            <tr class="empty-row">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            @endfor
        </tbody>
    </table>
</body>
</html>