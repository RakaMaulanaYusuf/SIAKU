@extends('main')

@section('title', 'Kode Akun')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col" 
    x-data="{ 
    searchTerm: '',
    accounts: {{ Js::from($accounts) }}
    }">
    <div class="flex overflow-hidden">
        <x-side-bar-customer></x-side-bar-customer>
        <div id="main-content" class="relative text-black ml-72 font-poppins w-full h-full overflow-y-auto">
            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-black">Kode Akun</h1>
                        <p class="text-sm text-gray-600 mt-1">Daftar kode akun perusahaan</p>
                    </div>
                    <div class="flex gap-3">
                        <div class="relative">
                            <input type="text" 
                                x-model="searchTerm"
                                placeholder="Cari kode atau nama akun..." 
                                class="w-64 px-4 py-2 pr-10 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <div class="absolute right-3 top-2.5 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-blue-600 text-white text-sm">
                                <th class="py-3 px-4 text-left border-b border-r w-20">ACCOUNT ID</th>
                                <th class="py-3 px-4 text-left border-b border-r w-96">NAMA AKUN</th>
                                <th class="py-3 px-4 text-left border-b border-r w-24">TABEL BANTUAN</th>
                                <th class="py-3 px-4 text-left border-b border-r w-28">POS SALDO</th>
                                <th class="py-3 px-4 text-left border-b border-r w-28">POS LAPORAN</th>
                                <th class="py-3 px-4 text-center border-b" colspan="2">SALDO AWAL</th>
                            </tr>
                            <tr class="bg-blue-600 text-white text-sm">
                                <th class="py-3 px-4 border-r" colspan="5"></th>
                                <th class="py-3 px-4 text-center border-r w-36">DEBIT</th>
                                <th class="py-3 px-4 text-center w-36">CREDIT</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            <!-- Existing Rows -->
                            <template x-for="(account, index) in accounts.filter(a => 
                                a.account_id.toString().includes(searchTerm.toLowerCase()) ||
                                a.name.toLowerCase().includes(searchTerm.toLowerCase())
                            )" :key="index">
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-r" x-text="account.account_id"></td>
                                    <td class="py-2 px-4 border-r" x-text="account.name"></td>
                                    <td class="py-2 px-4 border-r" x-text="account.helper_table"></td>
                                    <td class="py-2 px-4 border-r" x-text="account.balance_type"></td>
                                    <td class="py-2 px-4 border-r" x-text="account.report_type"></td>
                                    <td class="py-2 px-4 border-r text-right">
                                        <span x-text="account.debit ? 'Rp. ' + new Intl.NumberFormat('id-ID').format(account.debit) : '-'"></span>
                                    </td>
                                    <td class="py-2 px-4 text-right">
                                        <span x-text="account.credit ? 'Rp. ' + new Intl.NumberFormat('id-ID').format(account.credit) : '-'"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <!-- Footer with Totals -->
                        <tfoot class="bg-gray-50 text-sm">
                            <tr>
                                <td colspan="5" class="py-2 px-4 text-right font-medium border-r">Total Saldo Awal:</td>
                                <td class="py-2 px-4 text-right font-medium border-r" 
                                    x-text="'Rp. ' + new Intl.NumberFormat('id-ID').format(accounts.reduce((sum, account) => sum + (Number(account.debit) || 0), 0))">
                                </td>
                                <td class="py-2 px-4 text-right font-medium"
                                    x-text="'Rp. ' + new Intl.NumberFormat('id-ID').format(accounts.reduce((sum, account) => sum + (Number(account.credit) || 0), 0))">
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-6">
                    <button onclick="window.location.href='{{ route('kodeakun.download-pdf') }}'" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span>PRINT</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection