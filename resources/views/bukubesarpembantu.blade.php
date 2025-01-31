@extends('main')

@section('title', 'Buku Besar Pembantu')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col" x-data="{ 
    searchTerm: '',
    selectedAccount: '',
    accounts: {{ Js::from($accounts) }},
    transactions: {{ Js::from($transactions) }},

    loadTransactions() {
        if (!this.selectedAccount) {
            this.transactions = [];
            return;
        }
        
        fetch(`/bukubesarpembantu/transactions?helper_id=${this.selectedAccount}`)
            .then(response => response.json())
            .then(data => {
                this.transactions = data;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memuat data');
            });
    }
}" @change="loadTransactions">
    <div class="flex overflow-hidden">
        <x-side-bar-menu></x-side-bar-menu>
        <div id="main-content" class="relative text-black ml-72 font-poppins w-full h-full overflow-y-auto">
            <x-nav-bar></x-nav-bar>
            
            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-black">Buku Besar Pembantu</h1>
                        <div class="flex items-center gap-4 mt-2">
                            <select x-model="selectedAccount" 
                                class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Kode Bantu</option>
                                <template x-for="account in accounts" :key="account.code">
                                    <option :value="account.code" x-text="`${account.code} - ${account.name}`"></option>
                                </template>
                            </select>
                            <span x-show="selectedAccount" x-text="accounts.find(a => a.code === selectedAccount)?.name" 
                                class="text-gray-600"></span>
                        </div>
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

                <!-- Table Section -->
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-blue-600 text-white text-sm">
                                <th class="py-3 px-4 text-center border-r w-16">NO</th>
                                <th class="py-3 px-4 text-center border-r w-32">TANGGAL</th>
                                <th class="py-3 px-4 text-center border-r w-32">BUKTI TRANSAKSI</th>
                                <th class="py-3 px-4 text-center border-r">KETERANGAN</th>
                                <th class="py-3 px-4 text-center border-r w-40">DEBET</th>
                                <th class="py-3 px-4 text-center border-r w-40">KREDIT</th>
                                <th class="py-3 px-4 text-center border-r w-40">SALDO</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm" x-show="transactions.length > 0">
                            <template x-for="(transaction, index) in transactions.filter(t => 
                                t.description?.toLowerCase().includes(searchTerm.toLowerCase()) ||
                                t.bukti?.toLowerCase().includes(searchTerm.toLowerCase()))" 
                                :key="index">
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 text-center border-r" x-text="transaction.no"></td>
                                    <td class="py-2 px-4 text-center border-r" x-text="new Date(transaction.date).toLocaleDateString('id-ID')"></td>
                                    <td class="py-2 px-4 text-center border-r" x-text="transaction.bukti"></td>
                                    <td class="py-2 px-4 border-r" x-text="transaction.description"></td>
                                    <td class="py-2 px-4 text-right border-r" x-text="transaction.debit ? new Intl.NumberFormat('id-ID').format(transaction.debit) : '-'"></td>
                                    <td class="py-2 px-4 text-right border-r" x-text="transaction.credit ? new Intl.NumberFormat('id-ID').format(transaction.credit) : '-'"></td>
                                    <td class="py-2 px-4 text-right border-r" x-text="new Intl.NumberFormat('id-ID').format(transaction.balance)"></td>
                                </tr>
                            </template>
                        </tbody>
                        <tbody x-show="transactions.length === 0">
                            <tr>
                                <td colspan="7" class="py-4 px-4 text-center text-gray-500">
                                    Tidak ada data transaksi untuk kode bantu yang dipilih
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50 text-sm" x-show="transactions.length > 0">
                            <tr>
                                <td colspan="4" class="py-2 px-4 text-right font-medium border-r">Total:</td>
                                <td class="py-2 px-4 text-right font-medium border-r" 
                                    x-text="new Intl.NumberFormat('id-ID').format(transactions.reduce((sum, t) => sum + (Number(t.debit) || 0), 0))">
                                </td>
                                <td class="py-2 px-4 text-right font-medium border-r"
                                    x-text="new Intl.NumberFormat('id-ID').format(transactions.reduce((sum, t) => sum + (Number(t.credit) || 0), 0))">
                                </td>
                                <td class="py-2 px-4 text-right font-medium border-r"
                                    x-text="new Intl.NumberFormat('id-ID').format(transactions[transactions.length - 1]?.balance || 0)">
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <!-- Print Button -->
                <div class="flex justify-between mt-6">
                    <button 
                        @click="window.location.href = `/bukubesarpembantu/pdf?helper_id=${selectedAccount}`"
                        :disabled="!selectedAccount"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
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