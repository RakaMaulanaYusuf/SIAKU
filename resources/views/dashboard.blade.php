@extends('main')

@section('title', 'Dashboard')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex overflow-hidden">
        <x-side-bar-menu></x-side-bar-menu>
        
        <div id="main-content" class="relative text-black font-poppins w-full h-full overflow-y-auto">
            <!-- Current Company Indicator -->
            <x-nav-bar></x-nav-bar>

            <!-- Header Box -->
            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-black">Dashboard</h1>
                        <p class="text-gray-600 mt-1">Overview keuangan perusahaan</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Periode</p>
                            <p class="font-semibold">{{ $currentMonth->format('F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 p-6">
                <!-- Total Pendapatan -->
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Pendapatan</p>
                            <h3 class="text-2xl font-bold mt-1">Rp {{ number_format($totalPendapatanCurrent, 0, ',', '.') }}</h3>
                            <p class="{{ $pendapatanPercentage >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm mt-1">
                                {{ $pendapatanPercentage >= 0 ? '+' : '' }}{{ number_format($pendapatanPercentage, 1) }}% dari bulan lalu
                            </p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Pengeluaran -->
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Pengeluaran</p>
                            <h3 class="text-2xl font-bold mt-1">Rp {{ number_format($totalPengeluaranCurrent, 0, ',', '.') }}</h3>
                            <p class="{{ $pengeluaranPercentage >= 0 ? 'text-red-500' : 'text-green-500' }} text-sm mt-1">
                                {{ $pengeluaranPercentage >= 0 ? '+' : '' }}{{ number_format($pengeluaranPercentage, 1) }}% dari bulan lalu
                            </p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-lg">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Laba Bersih -->
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Laba Bersih</p>
                            <h3 class="text-2xl font-bold mt-1">Rp {{ number_format($labaBersihCurrent, 0, ',', '.') }}</h3>
                            <p class="{{ $labaBersihPercentage >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm mt-1">
                                {{ $labaBersihPercentage >= 0 ? '+' : '' }}{{ number_format($labaBersihPercentage, 1) }}% dari bulan lalu
                            </p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Aset -->
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Aset</p>
                            <h3 class="text-2xl font-bold mt-1">Rp {{ number_format($totalAset, 0, ',', '.') }}</h3>
                            <p class="text-green-500 text-sm mt-1">+{{ number_format($asetPercentage, 1) }}% dari bulan lalu</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Recent Transactions & Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                <!-- Recent Transactions -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold">Transaksi Terbaru</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="p-2 bg-green-100 rounded-lg">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium">Pendapatan Jasa</p>
                                        <p class="text-sm text-gray-500">10 Jan 2025</p>
                                    </div>
                                </div>
                                <p class="font-semibold text-green-600">+Rp 15.000.000</p>
                            </div>
                            <!-- Add more transactions -->
                        </div>
                    </div>
                </div>

                <!-- Account Summary -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold">Ringkasan Akun</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Piutang</span>
                                <span class="font-medium">Rp 35.000.000</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Hutang</span>
                                <span class="font-medium">Rp 25.000.000</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kas & Bank</span>
                                <span class="font-medium">Rp 150.000.000</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection