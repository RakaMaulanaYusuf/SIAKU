@extends('main')

@section('title', 'Jurnal Umum')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col" x-data="{ openDrawer: false }">
    <div class="flex overflow-hidden">
        <x-side-bar-menu></x-side-bar-menu>
        <div id="main-content" class="relative text-black ml-72 font-poppins w-full h-full overflow-y-auto">
            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Jurnal Umum</h1>
                        <p class="text-gray-600 text-sm">Mikrolet Selamet</p>
                    </div>
                    <!-- Tombol Tambah -->
                    <button 
                        @click="openDrawer = true"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Tambah Transaksi</span>
                    </button>
                </div>

                <!-- Tabel Jurnal -->
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-200">
                        <thead>
                            <tr class="bg-blue-600 text-white">
                                <th class="border border-blue-500 px-4 py-2 w-24">TANGGAL</th>
                                <th class="border border-blue-500 px-4 py-2 w-32">BUKTI TRANSAKSI</th>
                                <th class="border border-blue-500 px-4 py-2">KETERANGAN</th>
                                <th colspan="2" class="border border-blue-500 px-4 py-2">POS AKUN</th>
                                <th colspan="2" class="border border-blue-500 px-4 py-2">KODE BANTU</th>
                                <th class="border border-blue-500 px-4 py-2 w-32">DEBET</th>
                                <th class="border border-blue-500 px-4 py-2 w-32">KREDIT</th>
                            </tr>
                            <tr class="bg-blue-600 text-white">
                                <th class="border border-blue-500 px-4 py-2"></th>
                                <th class="border border-blue-500 px-4 py-2"></th>
                                <th class="border border-blue-500 px-4 py-2"></th>
                                <th class="border border-blue-500 px-4 py-2">NAMA AKUN</th>
                                <th class="border border-blue-500 px-4 py-2">KODE</th>
                                <th class="border border-blue-500 px-4 py-2">NAMA</th>
                                <th class="border border-blue-500 px-4 py-2">KODE</th>
                                <th class="border border-blue-500 px-4 py-2"></th>
                                <th class="border border-blue-500 px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="border border-gray-200 px-4 py-2 text-center">01-06-18</td>
                                <td class="border border-gray-200 px-4 py-2">MS 001</td>
                                <td class="border border-gray-200 px-4 py-2">Setor Uang Modal Perusahaan</td>
                                <td class="border border-gray-200 px-4 py-2">Kas</td>
                                <td class="border border-gray-200 px-4 py-2 text-center">11</td>
                                <td class="border border-gray-200 px-4 py-2"></td>
                                <td class="border border-gray-200 px-4 py-2"></td>
                                <td class="border border-gray-200 px-4 py-2 text-right font-medium">1.000</td>
                                <td class="border border-gray-200 px-4 py-2 text-right"></td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="border border-gray-200 px-4 py-2"></td>
                                <td class="border border-gray-200 px-4 py-2"></td>
                                <td class="border border-gray-200 px-4 py-2">Setor Uang Modal Perusahaan</td>
                                <td class="border border-gray-200 px-4 py-2">Modal Mas Selamet</td>
                                <td class="border border-gray-200 px-4 py-2 text-center">31</td>
                                <td class="border border-gray-200 px-4 py-2"></td>
                                <td class="border border-gray-200 px-4 py-2"></td>
                                <td class="border border-gray-200 px-4 py-2 text-right"></td>
                                <td class="border border-gray-200 px-4 py-2 text-right font-medium">1.000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-6">
                    <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span>PRINT</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Drawer for adding transactions -->
        <div x-show="openDrawer" 
             class="fixed inset-0 overflow-hidden z-50"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 overflow-hidden">
                <!-- Background overlay -->
                <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     @click="openDrawer = false"></div>

                <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                    <div class="relative w-screen max-w-md">
                        <!-- Drawer panel -->
                        <div class="h-full flex flex-col bg-white shadow-xl">
                            <div class="flex-1 overflow-y-auto p-6">
                                <div class="flex items-start justify-between">
                                    <h2 class="text-lg font-medium text-gray-900">Tambah Transaksi</h2>
                                    <button @click="openDrawer = false" class="text-gray-400 hover:text-gray-500">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Form -->
                                <div class="mt-6">
                                    <form class="space-y-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                                            <input type="date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Bukti Transaksi</label>
                                            <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: MS 001">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                                            <textarea class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="3"></textarea>
                                        </div>

                                        <!-- Detail Transaksi -->
                                        <div class="space-y-4">
                                            <label class="block text-sm font-medium text-gray-700">Detail Transaksi</label>
                                            
                                            <!-- Debet Entry -->
                                            <div class="bg-gray-50 p-4 rounded-lg space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Akun Debet</label>
                                                    <select class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                        <option value="">Pilih Akun</option>
                                                        <option value="11">Kas</option>
                                                        <option value="12">Piutang Dagang</option>
                                                        <!-- Add more options based on your chart of accounts -->
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Jumlah Debet</label>
                                                    <input type="number" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                            </div>

                                            <!-- Kredit Entry -->
                                            <div class="bg-gray-50 p-4 rounded-lg space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Akun Kredit</label>
                                                    <select class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                        <option value="">Pilih Akun</option>
                                                        <option value="31">Modal</option>
                                                        <option value="41">Pendapatan</option>
                                                        <!-- Add more options based on your chart of accounts -->
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Jumlah Kredit</label>
                                                    <input type="number" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-6">
                                            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                                Simpan Transaksi
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection