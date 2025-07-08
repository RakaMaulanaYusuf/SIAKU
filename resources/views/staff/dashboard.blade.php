@extends('main')

@section('title', 'Dashboard')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex overflow-hidden">
        <x-side-bar-menu></x-side-bar-menu>
        
        <div id="main-content" class="relative text-black font-poppins w-full h-full overflow-y-auto">
            <x-nav-bar></x-nav-bar>

            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-black">Dashboard</h1>
                        <p class="text-gray-600 mt-1">Overview keuangan perusahaan</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Perusahaan</p>
                            <p class="font-semibold">{{ $company ? $company->name : 'Pilih Perusahaan' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Periode</p>
                            <p class="font-semibold">{{ $currentCompanyPeriod ? $currentCompanyPeriod->getFormattedPeriodAttribute() : 'Pilih Periode' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 p-6">
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Pendapatan</p>
                            <h3 class="text-2xl font-bold mt-1">Rp {{ number_format($totalPendapatanCurrent, 0, ',', '.') }}</h3>
                            @if($totalPendapatanCurrent > 0 || $pendapatanPercentage !== 0) 
                                <p class="{{ $pendapatanPercentage >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm mt-1">
                                    {{ $pendapatanPercentage >= 0 ? '+' : '' }}{{ number_format($pendapatanPercentage, 1) }}% dari bulan lalu
                                </p>
                            @else
                                <p class="text-gray-500 text-sm mt-1">Tidak ada data perbandingan</p>
                            @endif
                        </div>
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Pengeluaran</p>
                            <h3 class="text-2xl font-bold mt-1">Rp {{ number_format($totalPengeluaranCurrent, 0, ',', '.') }}</h3>
                            @if($totalPengeluaranCurrent > 0 || $pengeluaranPercentage !== 0)
                                <p class="{{ $pengeluaranPercentage >= 0 ? 'text-red-500' : 'text-green-500' }} text-sm mt-1">
                                    {{ $pengeluaranPercentage >= 0 ? '+' : '' }}{{ number_format($pengeluaranPercentage, 1) }}% dari bulan lalu
                                </p>
                            @else
                                <p class="text-gray-500 text-sm mt-1">Tidak ada data perbandingan</p>
                            @endif
                        </div>
                        <div class="p-3 bg-red-100 rounded-lg">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Laba Bersih</p>
                            <h3 class="text-2xl font-bold mt-1">Rp {{ number_format($labaBersihCurrent, 0, ',', '.') }}</h3>
                            @if($labaBersihCurrent > 0 || $labaBersihPercentage !== 0)
                                <p class="{{ $labaBersihPercentage >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm mt-1">
                                    {{ $labaBersihPercentage >= 0 ? '+' : '' }}{{ number_format($labaBersihPercentage, 1) }}% dari bulan lalu
                                </p>
                            @else
                                <p class="text-gray-500 text-sm mt-1">Tidak ada data perbandingan</p>
                            @endif
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Aset</p>
                            <h3 class="text-2xl font-bold mt-1">Rp {{ number_format($totalAset, 0, ',', '.') }}</h3>
                            @if($totalAset > 0 || $asetPercentage !== 0)
                                <p class="{{ $asetPercentage >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm mt-1">
                                    {{ $asetPercentage >= 0 ? '+' : '' }}{{ number_format($asetPercentage, 1) }}% dari bulan lalu
                                </p>
                            @else
                                <p class="text-gray-500 text-sm mt-1">Tidak ada data perbandingan</p>
                            @endif
                        </div>
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold">Transaksi Terbaru</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse($recentTransactions as $transaction)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="p-2 {{ $transaction->debit > 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} rounded-lg">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($transaction->debit > 0)
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                            @endif
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium">{{ $transaction->description }}</p> 
                                        <p class="text-sm text-gray-500">{{ $transaction->date->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <p class="font-semibold {{ $transaction->debit > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->debit > 0 ? '+' : '-' }}Rp {{ number_format(abs($transaction->debit > 0 ? $transaction->debit : $transaction->credit), 0, ',', '.') }}
                                </p>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center">Tidak ada transaksi terbaru.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold">Tren Pendapatan & Pengeluaran (6 Bulan)</h2>
                    </div>
                    <div class="p-6">
                        <canvas id="revenueExpensesChart" class="w-full h-64"></canvas>
                    </div>
                </div>

                {{-- <div class="bg-white rounded-xl shadow-sm">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold">Ringkasan Saldo Akun Kunci</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse($keyAccountBalances as $account)
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ $account['name'] }}</span>
                                <span class="font-medium {{ $account['is_positive'] ? 'text-green-600' : 'text-red-600' }}">
                                    Rp {{ number_format($account['balance'], 0, ',', '.') }}
                                </span>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center">Tidak ada saldo akun kunci yang tersedia.</p>
                            @endforelse
                        </div>
                    </div>
                </div> --}}

            </div>
        </div>
    </div>
</div>

{{-- Script for Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const monthlySummary = @json($monthlySummary); 

        if (monthlySummary && monthlySummary.length > 0) {
            const labels = monthlySummary.map(item => item.period_label);
            const revenueData = monthlySummary.map(item => item.revenue);
            const expensesData = monthlySummary.map(item => item.expenses);

            const ctx = document.getElementById('revenueExpensesChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Pendapatan',
                            data: revenueData,
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.1,
                            fill: false,
                        },
                        {
                            label: 'Pengeluaran',
                            data: expensesData,
                            borderColor: 'rgb(255, 99, 132)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            tension: 0.1,
                            fill: false,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value, index, values) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection