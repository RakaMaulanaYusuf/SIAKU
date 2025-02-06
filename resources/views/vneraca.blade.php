@extends('main')

@section('title', 'Neraca')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col" x-data="{ 
    activeTab: 'aktiva_lancar',
    aktivaLancarRows: {{ Js::from($aktivalancar) }},
    aktivaTetapRows: {{ Js::from($aktivatetap) }},
    kewajibanRows: {{ Js::from($kewajiban) }},
    ekuitasRows: {{ Js::from($ekuitas) }},
    searchTerm: '',

    getTotalAktivaLancar() {
        return this.aktivaLancarRows.reduce((sum, row) => sum + (Number(row.amount) || 0), 0);
    },

    getTotalAktivaTetap() {
        return this.aktivaTetapRows.reduce((sum, row) => sum + (Number(row.amount) || 0), 0);
    },

    getTotalKewajiban() {
        return this.kewajibanRows.reduce((sum, row) => sum + (Number(row.amount) || 0), 0);
    },

    getTotalEkuitas() {
        return this.ekuitasRows.reduce((sum, row) => sum + (Number(row.amount) || 0), 0);
    },

    getTotalAktiva() {
        return this.getTotalAktivaLancar() + this.getTotalAktivaTetap();
    },

    getTotalPasiva() {
        return this.getTotalKewajiban() + this.getTotalEkuitas();
    },

    formatNumber(number) {
        return new Intl.NumberFormat('id-ID').format(Number(number) || 0);
    },

    getCurrentRows() {
        const rows = this.activeTab === 'aktiva_lancar' ? this.aktivaLancarRows : 
                    this.activeTab === 'aktiva_tetap' ? this.aktivaTetapRows : 
                    this.activeTab === 'kewajiban' ? this.kewajibanRows : 
                    this.ekuitasRows;
        return rows.filter(row => 
            row.name?.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
            row.account_id?.toLowerCase().includes(this.searchTerm.toLowerCase())
        );
    }
}">
    <div class="flex overflow-hidden">
        <x-side-bar-customer></x-side-bar-customer>
        <div id="main-content" class="relative text-black ml-72 font-poppins w-full h-full overflow-y-auto">
            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-black">Laporan Neraca</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ now()->translatedFormat('d F Y') }}</p>
                    </div>
                    <div class="relative">
                        <input type="text" x-model="searchTerm" placeholder="Cari data..." 
                               class="w-64 px-4 py-2 pr-10 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Tabs -->
                <div class="border-b border-gray-200 mb-6">
                    <div class="flex gap-4">
                        <button @click="activeTab = 'aktiva_lancar'"
                            :class="{'border-b-2 border-gray-600 text-gray-600': activeTab === 'aktiva_lancar'}"
                            class="py-2 px-4 font-medium hover:text-gray-600 transition-colors">
                            Aktiva Lancar
                        </button>
                        <button @click="activeTab = 'aktiva_tetap'"
                            :class="{'border-b-2 border-gray-600 text-gray-600': activeTab === 'aktiva_tetap'}"
                            class="py-2 px-4 font-medium hover:text-gray-600 transition-colors">
                            Aktiva Tetap
                        </button>
                        <button @click="activeTab = 'kewajiban'"
                            :class="{'border-b-2 border-gray-600 text-gray-600': activeTab === 'kewajiban'}"
                            class="py-2 px-4 font-medium hover:text-gray-600 transition-colors">
                            Kewajiban
                        </button>
                        <button @click="activeTab = 'ekuitas'"
                            :class="{'border-b-2 border-gray-600 text-gray-600': activeTab === 'ekuitas'}"
                            class="py-2 px-4 font-medium hover:text-gray-600 transition-colors">
                            Ekuitas
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
                            <template x-for="row in getCurrentRows()" :key="row.id">
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border" x-text="row.account_id"></td>
                                    <td class="py-2 px-4 border" x-text="row.name"></td>
                                    <td class="py-2 px-4 border text-right" x-text="formatNumber(row.amount)"></td>
                                </tr>
                            </template>

                            <!-- Summary Row -->
                            <tr class="bg-gray-50 text-black font-medium">
                                <td colspan="2" class="py-2 px-4 border font-medium text-center">
                                    <span x-show="activeTab === 'aktiva_lancar'">TOTAL AKTIVA LANCAR</span>
                                    <span x-show="activeTab === 'aktiva_tetap'">TOTAL AKTIVA TETAP</span>
                                    <span x-show="activeTab === 'kewajiban'">TOTAL KEWAJIBAN</span>
                                    <span x-show="activeTab === 'ekuitas'">TOTAL EKUITAS</span>
                                </td>
                                <td class="py-2 px-4 border"></td>
                                <td class="py-2 px-4 border text-right font-medium">
                                    <span x-show="activeTab === 'aktiva_lancar'" x-text="formatNumber(getTotalAktivaLancar())"></span>
                                    <span x-show="activeTab === 'aktiva_tetap'" x-text="formatNumber(getTotalAktivaTetap())"></span>
                                    <span x-show="activeTab === 'kewajiban'" x-text="formatNumber(getTotalKewajiban())"></span>
                                    <span x-show="activeTab === 'ekuitas'" x-text="formatNumber(getTotalEkuitas())"></span>
                                </td>
                            </tr>

                            <!-- Final Summary Row -->
                            <template x-if="activeTab === 'aktiva_tetap'">
                                <tr class="bg-gray-100 text-black font-bold">
                                    <td colspan="2" class="py-3 px-4 border text-center">TOTAL AKTIVA</td>
                                    <td class="py-3 px-4 border"></td>
                                    <td class="py-3 px-4 border text-right"
                                        :class="getTotalAktiva() >= 0 ? 'text-green-600' : 'text-red-600'" 
                                        x-text="formatNumber(getTotalAktiva())"></td>
                                </tr>
                            </template>
                            <template x-if="activeTab === 'ekuitas'">
                                <tr class="bg-gray-100 text-black font-bold">
                                    <td colspan="2" class="py-3 px-4 border text-center">TOTAL PASIVA</td>
                                    <td class="py-3 px-4 border"></td>
                                    <td class="py-3 px-4 border text-right"
                                        :class="getTotalPasiva() >= 0 ? 'text-green-600' : 'text-red-600'"     
                                        x-text="formatNumber(getTotalPasiva())"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
