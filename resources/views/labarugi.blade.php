@extends('main')

@section('title', 'Laba Rugi')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col" x-data="{ 
    activeTab: 'pendapatan',
    pendapatanRows: {{ Js::from($pendapatan) }},
    hppRows: {{ Js::from($hpp) }},
    operasionalRows: {{ Js::from($biaya) }},
    availableAccounts: {{ Js::from($availableAccounts) }},
    searchTerm: '',
    newRow: {
        account_id: '',
        name: '',
        amount: 0
    },

    updateNewRowName(accountId) {
        const account = this.availableAccounts.find(acc => acc.account_id === accountId);
        if (account) {
            this.newRow.name = account.name;
            this.newRow.amount = account.balance || 0;
        } else {
            this.newRow.name = '';
            this.newRow.amount = 0;
        }
    },

    async saveData() {
        if (!this.validateForm()) return;

        try {
            const response = await fetch('/labarugi', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    type: this.activeTab,
                    account_id: this.newRow.account_id,
                    name: this.newRow.name,
                    amount: this.newRow.amount
                })
            });

            const data = await response.json();
            
            if (data.success) {
                const newRowData = { 
                    id: data.data.id,
                    account_id: this.newRow.account_id,
                    name: this.newRow.name,
                    amount: Number(this.newRow.amount) || 0,
                    isEditing: false
                };

                if (this.activeTab === 'pendapatan') {
                    this.pendapatanRows.push(newRowData);
                } else if (this.activeTab === 'hpp') {
                    this.hppRows.push(newRowData);
                } else if (this.activeTab === 'operasional'){
                    this.operasionalRows.push(newRowData);
                }

                this.resetForm();
                alert('Data berhasil disimpan');
            } else {
                alert('Gagal menambahkan data: ' + data.message);
            }
        } catch (error) {
            alert('Terjadi kesalahan: ' + error.message);
        }
    },

    validateForm() {
        if (!this.newRow.account_id) {
            alert('Silakan pilih kode akun terlebih dahulu');
            return false;
        }
        return true;
    },

    resetForm() {
        this.newRow = {
            account_id: '',
            name: '',
            amount: 0
        };
    },

    startEdit(row) {
        row.originalData = { ...row };
        row.isEditing = true;
    },

    async saveEdit(row) {
        if (!this.validateForm(row)) return;

        try {
            const response = await fetch(`/labarugi/${this.activeTab}/${row.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(row)
            });

            const data = await response.json();
            
            if (data.success) {
                row.isEditing = false;
                delete row.originalData;
                Object.assign(row, data.data);
                alert('Data berhasil diupdate');
            } else {
                alert(data.message || 'Terjadi kesalahan saat menyimpan perubahan');
            }
        } catch (error) {
            alert('Terjadi kesalahan: ' + error.message);
        }
    },

    cancelEdit(row) {
        if (row.originalData) {
            Object.assign(row, row.originalData);
            delete row.originalData;
        }
        row.isEditing = false;
    },

    async deleteRow(row) {
        if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) return;

        try {
            const response = await fetch(`/labarugi/${this.activeTab}/${row.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                if (this.activeTab === 'pendapatan') {
                    this.pendapatanRows = this.pendapatanRows.filter(r => r.id !== row.id);
                } else if (this.activeTab === 'hpp') {
                    this.hppRows = this.hppRows.filter(r => r.id !== row.id);
                } else {
                    this.operasionalRows = this.operasionalRows.filter(r => r.id !== row.id);
                }
                alert('Data berhasil dihapus');
            }
        } catch (error) {
            alert('Terjadi kesalahan: ' + error.message);
        }
    },

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
        <x-side-bar-menu></x-side-bar-menu>
        <div id="main-content" class="relative text-black font-poppins w-full h-full overflow-y-auto">
            <x-nav-bar></x-nav-bar>
            
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
                            :class="{'border-b-2 border-blue-600 text-blue-600': activeTab === 'pendapatan'}"
                            class="py-2 px-4 font-medium hover:text-blue-600 transition-colors">
                            Pendapatan
                        </button>
                        <button @click="activeTab = 'hpp'"
                            :class="{'border-b-2 border-blue-600 text-blue-600': activeTab === 'hpp'}"
                            class="py-2 px-4 font-medium hover:text-blue-600 transition-colors">
                            HPP
                        </button>
                        <button @click="activeTab = 'operasional'"
                            :class="{'border-b-2 border-blue-600 text-blue-600': activeTab === 'operasional'}"
                            class="py-2 px-4 font-medium hover:text-blue-600 transition-colors">
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
                                <th class="py-3 px-4 text-center border w-20">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            <!-- Input Row -->
                            <tr class="bg-gray-50">
                                <td class="py-2 px-4 border">
                                    <select x-model="newRow.account_id" 
                                            @change="updateNewRowName($event.target.value)"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="">Pilih Kode Akun</option>
                                        <template x-for="account in availableAccounts" :key="account.account_id">
                                            <option :value="account.account_id" x-text="account.account_id"></option>
                                        </template>
                                    </select>
                                </td>
                                <td class="py-2 px-4 border">
                                    <input type="text" x-model="newRow.name" readonly
                                           class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm">
                                </td>
                                <td class="py-2 px-4 border">
                                    <input type="text" x-model="newRow.amount" readonly
                                           class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm text-right">
                                </td>
                                <td class="py-2 px-4 border"></td>
                                <td class="py-2 px-4 border text-center">
                                    <button @click="saveData()" 
                                            class="p-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>

                            <!-- Existing Rows -->
                            <template x-for="row in getCurrentRows()" :key="row.id">
                                <tr :class="{'bg-blue-50': row.isEditing}" class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border" x-text="row.account_id"></td>
                                    <td class="py-2 px-4 border" x-text="row.name"></td>
                                    <td class="py-2 px-4 border text-right" x-text="formatNumber(row.amount)"></td>
                                    <td class="py-2 px-4 border"></td>
                                    <td class="py-2 px-4 border text-center">
                                        <template x-if="!row.isEditing">
                                            <div class="flex justify-center gap-2">
                                                <button @click="startEdit(row)" 
                                                        class="p-1 text-blue-600 hover:bg-blue-50 rounded">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <button @click="deleteRow(row)" 
                                                class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="row.isEditing">
                                        <div class="flex justify-center gap-2">
                                            <button @click="saveEdit(row)" 
                                                    class="p-1 text-green-600 hover:bg-green-50 rounded">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                            <button @click="cancelEdit(row)" 
                                                    class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </td>
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
                            <td class="py-2 px-4 border"></td>
                        </tr>

                        <!-- Final Summary (Only shown in Operasional tab) -->
                        <template x-if="activeTab === 'operasional'">
                            <tr class="bg-gray-100 text-black font-bold">
                                <td colspan="2" class="py-3 px-4 border text-center">LABA/RUGI BERSIH</td>
                                <td class="py-3 px-4 border"></td>
                                <td class="py-3 px-4 border text-right" 
                                    :class="getLabaBersih() >= 0 ? 'text-green-600' : 'text-red-600'"
                                    x-text="formatNumber(getLabaBersih())">
                                </td>
                                <td class="py-3 px-4 border"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Print Button -->
            <div class="flex justify-between mt-6">
                <button @click="window.location.href = '/labarugi/pdf'"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2">
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