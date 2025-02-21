@extends('main')

@section('title', 'Laba Rugi')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col" x-data="{ 
    activeTab: 'pendapatan',
    pendapatanRows: {{ Js::from($pendapatan) }},
    hppRows: {{ Js::from($hpp) }},
    operasionalRows: {{ Js::from($biaya) }},
    searchTerm: '',

    getTotalPendapatan() {
        return this.pendapatanRows.reduce((sum, row) => sum + (Number(row.amount) || 0), 0);
    },

    getTotalHPP() {
        return this.hppRows.reduce((sum, row) => sum + (Number(row.amount) || 0), 0);
    },

    getTotalOperasional() {
        return this.operasionalRows.reduce((sum, row) => sum + (Number(row.amount) || 0), 0);
    },

    getLabaBersih() {
        return this.getTotalPendapatan() - (this.getTotalHPP() + this.getTotalOperasional());
    },

    formatNumber(number) {
        return new Intl.NumberFormat('id-ID').format(Number(number) || 0);
    },

    getCurrentRows() {
        const rows = this.activeTab === 'pendapatan' ? this.pendapatanRows : 
                    this.activeTab === 'hpp' ? this.hppRows : 
                    this.operasionalRows;
        return rows.filter(row => 
            row.name?.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
            row.account_id?.toLowerCase().includes(this.searchTerm.toLowerCase())
        );
    }
}">
    <div class="flex overflow-hidden">
        <x-side-bar-viewer></x-side-bar-viewer>
        <div id="main-content" class="relative text-black font-poppins w-full h-full overflow-y-auto">
            <x-nav-bar-viewer></x-nav-bar-viewer>
            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-black">Laporan Laba Rugi</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ now()->translatedFormat('d F Y') }}</p>
                    </div>
                    <div class="flex gap-3">
                        <div class="relative">
                            <input type="text" 
                                x-model="searchTerm"
                                placeholder="Cari data..." 
                                class="w-64 px-4 py-2 pr-10 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <div class="absolute right-3 top-2.5 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="border-b border-gray-200 mb-6">
                    <div class="flex gap-4">
                        <button @click="activeTab = 'pendapatan'"
                            :class="{'border-b-2 border-gray-600 text-gray-600': activeTab === 'pendapatan'}"
                            class="py-2 px-4 font-medium hover:text-gray-600 transition-colors">
                            Pendapatan
                        </button>
                        <button @click="activeTab = 'hpp'"
                            :class="{'border-b-2 border-gray-600 text-gray-600': activeTab === 'hpp'}"
                            class="py-2 px-4 font-medium hover:text-gray-600 transition-colors">
                            HPP
                        </button>
                        <button @click="activeTab = 'operasional'"
                            :class="{'border-b-2 border-gray-600 text-gray-600': activeTab === 'operasional'}"
                            class="py-2 px-4 font-medium hover:text-gray-600 transition-colors">
                            Biaya Operasional
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-blue-600 text-white text-sm">
                                <th class="py-3 px-4 text-left border w-32">KODE AKUN</th>
                                <th class="py-3 px-4 text-left border">NAMA AKUN</th>
                                <th class="py-3 px-4 text-right border w-48">JUMLAH</th>
                                <th class="py-3 px-4 text-right border w-48">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            <!-- Existing Rows -->
                            <template x-for="row in getCurrentRows()" :key="row.id">
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border" x-text="row.account_id"></td>
                                    <td class="py-2 px-4 border" x-text="row.name"></td>
                                    <td class="py-2 px-4 border text-right" x-text="formatNumber(row.amount)"></td>
                                    {{-- <td class="py-2 px-4 border text-right" x-text="formatNumber(row.balance)"></td> --}}
                                </tr>
                            </template>

                            <!-- Summary Rows -->
                            <tr class="bg-gray-50 text-black">
                                <td colspan="2" class="py-2 px-4 border font-medium text-center">
                                    <span x-show="activeTab === 'pendapatan'">TOTAL PENDAPATAN</span>
                                    <span x-show="activeTab === 'hpp'">TOTAL HPP</span>
                                    <span x-show="activeTab === 'operasional'">TOTAL BIAYA OPERASIONAL</span>
                                </td>
                                <td class="py-2 px-4 border"></td>
                                <td class="py-2 px-4 border text-right font-medium">
                                    <span x-show="activeTab === 'pendapatan'" x-text="formatNumber(getTotalPendapatan())"></span>
                                    <span x-show="activeTab === 'hpp'" x-text="formatNumber(getTotalHPP())"></span>
                                    <span x-show="activeTab === 'operasional'" x-text="formatNumber(getTotalOperasional())"></span>
                                </td>
                            </tr>

                            <!-- Final Summary -->
                            <template x-if="activeTab === 'operasional'">
                                <tr class="bg-gray-100 text-black font-bold">
                                    <td colspan="2" class="py-3 px-4 border text-center">LABA/RUGI BERSIH</td>
                                    <td class="py-3 px-4 border"></td>
                                    <td class="py-3 px-4 border text-right" 
                                        :class="getLabaBersih() >= 0 ? 'text-green-600' : 'text-red-600'"
                                        x-text="formatNumber(getLabaBersih())">
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                
                <!-- Print Button -->
                <div class="flex justify-end mt-6">
                    <button @click="window.location.href = '/vlabarugi/pdf'"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Print PDF</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection