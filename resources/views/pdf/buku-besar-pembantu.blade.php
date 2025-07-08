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
        
        .date, .period { /* Tambahkan .period untuk gaya konsisten */
            font-size: 10px;
            margin-bottom: 5px;
        }
        
        /* Account Info for each general ledger */
        .account-section {
            margin-bottom: 20px; /* Space between different ledger accounts */
            page-break-inside: avoid; /* Keep entire section together on one page if possible */
        }

        .account-info {
            margin-top: 15px; /* Add some space above each account's info */
            margin-bottom: 5px;
        }
        
        .account-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        
        .account-table td {
            border: 1px solid #000;
            padding: 5px;
            font-weight: bold;
            vertical-align: top;
        }
        
        .account-name-cell { /* Disesuaikan untuk nama */
            width: 50%;
            padding-left: 10px;
        }
        
        .account-code-cell { /* Disesuaikan untuk kode */
            width: 25%;
            text-align: center;
        }
        
        .account-type-cell { /* Disesuaikan untuk status */
            width: 25%;
            text-align: center;
        }
        
        /* Main Transactions Table */
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0; 
        }
        
        .transactions-table th, .transactions-table td {
            border: 1px solid #000;
            padding: 3px;
            text-align: left;
            vertical-align: top;
        }
        
        .transactions-table th {
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
    </style>
</head>
<body>
    {{-- Header Dokumen --}}
    <div class="header">
        <div class="company-name">{{ strtoupper($companyName) }}</div>
        <div class="title">{{ $title }}</div>
        <div class="period">Periode: {{ $periodName }}</div> {{-- Tampilkan Periode --}}
        <div class="date">Tanggal Cetak: {{ strtoupper($date) }}</div>
    </div>

    {{-- Loop melalui setiap data pembantu Buku Besar --}}
    @foreach($bukuBesarPembantuData as $helperData)
        <div class="account-section">
            {{-- Informasi Kode Bantu --}}
            <div class="account-info">
                <table class="account-table">
                    <tr>
                        <td class="account-name-cell">NAMA: {{ $helperData['helper_name'] }}</td>
                        <td class="account-code-cell">KODE: {{ $helperData['helper_id'] }}</td>
                        <td class="account-type-cell">STATUS: {{ $helperData['status'] }}</td>
                    </tr>
                </table>
            </div>

            {{-- Tabel Transaksi untuk Kode Bantu Saat Ini --}}
            <table class="transactions-table">
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
                        <td class="text-center">-</td> {{-- Debit awal tidak ditampilkan di sini --}}
                        <td class="text-center">-</td> {{-- Kredit awal tidak ditampilkan di sini --}}
                        <td class="text-right">
                            {{-- Saldo awal dari KodeBantu model --}}
                            {{ number_format($helperData['initial_balance'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>

                    {{-- Data Transaksi --}}
                    @foreach($helperData['transactions'] as $transaction)
                    <tr>
                        <td class="text-center">{{ $transaction['no'] }}</td>
                        <td class="text-center">{{ $transaction['date'] }}</td>
                        <td class="text-center">{{ $transaction['bukti'] }}</td>
                        <td>{{ $transaction['description'] }}</td>
                        <td class="text-right">
                            @if(isset($transaction['debit']) && $transaction['debit'] > 0)
                                {{ number_format($transaction['debit'], 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            @if(isset($transaction['credit']) && $transaction['credit'] > 0)
                                {{ number_format($transaction['credit'], 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($transaction['balance'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach

                    {{-- Menambahkan baris kosong setelah transaksi jika diperlukan --}}
                    {{-- @php
                        $rowsCount = 1 + count($helperData['transactions']);
                        $minRowsForDisplay = 15; // Contoh minimal baris untuk tampilan
                    @endphp
                    @for($i = $rowsCount; $i < $minRowsForDisplay; $i++)
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor --}}
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>