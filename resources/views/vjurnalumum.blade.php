@extends('main')

@section('title', 'Jurnal Umum')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col" x-data="{ 
    searchTerm: '',
    transactions: {{ Js::from($journals) }},
    accounts: {{ Js::from($accounts) }},
    helpers: {{ Js::from($helpers) }},
    
    getAccountName(account_id) {
        const account = this.accounts.find(a => a.account_id === account_id);
        return account ? account.name : '-';
    },

    getAccountCode(account_id) {
        const account = this.accounts.find(a => a.account_id === account_id);
        return account ? account.account_id : '-';
    },

    getHelperName(helper_id) {
        const helper = this.helpers.find(h => h.helper_id === helper_id);
        return helper ? helper.name : '-';
    },

    getHelperCode(helper_id) {
        const helper = this.helpers.find(h => h.helper_id === helper_id);
        return helper ? helper.helper_id : '-';
    }
}">
    <div class="flex overflow-hidden">
        <x-side-bar-customer></x-side-bar-customer>
        <div id="main-content" class="relative text-black ml-72 font-poppins w-full h-full overflow-y-auto">      
            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-black">Jurnal Umum</h1>
                        <p class="text-sm text-gray-600 mt-1">Daftar jurnal umum perusahaan</p>
                    </div>
                    <div class="flex gap-3">
                        <div class="relative">
                            <input type="text" 
                                x-model="searchTerm"
                                placeholder="Cari transaksi..." 
                                class="w-64 px-4 py-2 pr-10 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <div class="absolute right-3 top-2.5 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tabel Jurnal -->
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-blue-600 text-white text-sm">
                                <th class="border border-blue-500 px-10 py-2 w-24">TANGGAL</th>
                                <th class="border border-blue-500 px-4 py-2 w-32">BUKTI TRANSAKSI</th>
                                <th class="border border-blue-500 px-4 py-2">KETERANGAN</th>
                                <th colspan="2" class="border border-blue-500 px-4 py-2">POS AKUN</th>
                                <th colspan="2" class="border border-blue-500 px-4 py-2">KODE BANTU</th>
                                <th class="border border-blue-500 px-10 py-2 w-32">DEBET</th>
                                <th class="border border-blue-500 px-10 py-2 w-32">KREDIT</th>
                            </tr>
                            <tr class="bg-blue-600 text-white text-sm">
                                <th colspan="3" class="border border-blue-500 px-4 py-2"></th>
                                <th class="border border-blue-500 px-4 py-2">NAMA AKUN</th>
                                <th class="border border-blue-500 px-4 py-2">KODE</th>
                                <th class="border border-blue-500 px-4 py-2">NAMA</th>
                                <th class="border border-blue-500 px-4 py-2">KODE</th>
                                <th colspan="2" class="border border-blue-500 px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Existing Rows -->
                            <template x-for="transaction in transactions.filter(t => 
                                t.description?.toLowerCase().includes(searchTerm.toLowerCase()) ||
                                t.transaction_proof?.toLowerCase().includes(searchTerm.toLowerCase()))"
                                :key="transaction.id">
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-r" x-text="transaction.date"></td>
                                    <td class="py-2 px-4 border-r" x-text="transaction.transaction_proof"></td>
                                    <td class="py-2 px-4 border-r" x-text="transaction.description"></td>
                                    <td class="py-2 px-4 border-r" x-text="getAccountName(transaction.account_id)"></td>
                                    <td class="py-2 px-4 border-r" x-text="getAccountCode(transaction.account_id)"></td>
                                    <td class="py-2 px-4 border-r" x-text="getHelperName(transaction.helper_id)"></td>
                                    <td class="py-2 px-4 border-r" x-text="getHelperCode(transaction.helper_id)"></td>
                                    <td class="py-2 px-4 border-r text-right" x-text="transaction.debit ? 'Rp. ' + new Intl.NumberFormat('id-ID').format(transaction.debit) : '-'"></td>
                                    <td class="py-2 px-4 border-r text-right" x-text="transaction.credit ? 'Rp. ' + new Intl.NumberFormat('id-ID').format(transaction.credit) : '-'"></td>
                                </tr>
                            </template>
                        </tbody>
                        
                        <!-- Footer with Totals -->
                        <tfoot class="bg-gray-50 text-sm">
                            <tr>
                                <td colspan="7" class="py-2 px-10 text-right font-medium border-r">Total:</td>
                                <td class="py-2 px-10 text-right font-medium border-r" 
                                    x-text="'Rp. ' + new Intl.NumberFormat('id-ID').format(
                                        transactions.reduce((sum, t) => sum + (parseFloat(t.debit) || 0), 0)
                                    )">
                                </td>
                                <td class="py-2 px-10 text-right font-medium"
                                    x-text="'Rp. ' + new Intl.NumberFormat('id-ID').format(
                                        transactions.reduce((sum, t) => sum + (parseFloat(t.credit) || 0), 0)
                                    )">
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-6">
                    <button onclick="window.location.href='{{ route('vjurnalumum.download-pdf') }}'"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>PRINT</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection