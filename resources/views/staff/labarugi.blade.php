@extends('main')

@section('title', 'Laba Rugi')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('page')
<style>
.swal2-confirm {
    color: white !important;
    background-color: #3085d6 !important;
}

.swal2-cancel {
    color: white !important;
    background-color: #d33 !important;
}

.swal2-styled {
    color: white !important;
}
</style>
<div class="bg-gray-50 min-h-screen flex flex-col" x-data="{ 
    pendapatanRows: {{ Js::from($pendapatan->map(function($item) {
        return array_merge($item, ['amount' => $item['balance']]);
    })) }},
    hppRows: {{ Js::from($hpp->map(function($item) {
        return array_merge($item, ['amount' => $item['balance']]);
    })) }},
    operasionalRows: {{ Js::from($biaya->map(function($item) {
        return array_merge($item, ['amount' => $item['balance']]);
    })) }},
    availableAccounts: {{ Js::from($availableAccounts) }},
    searchTerm: '',
    activeSection: '',
    
    // Objek newRow terpisah untuk setiap tabel
    newRowPendapatan: {
        account_id: '',
        name: '',
        amount: 0
    },
    newRowHpp: {
        account_id: '',
        name: '',
        amount: 0
    },
    newRowOperasional: {
        account_id: '',
        name: '',
        amount: 0
    },

    updateNewRowName(accountId, section) {
        const account = this.availableAccounts.find(acc => acc.account_id === accountId);
        let newRowKey;
        
        switch(section) {
            case 'pendapatan':
                newRowKey = 'newRowPendapatan';
                break;
            case 'hpp':
                newRowKey = 'newRowHpp';
                break;
            case 'operasional':
                newRowKey = 'newRowOperasional';
                break;
        }
        
        if (account) {
            this[newRowKey].account_id = accountId;
            this[newRowKey].name = account.name;
            this[newRowKey].amount = account.balance || 0;
        } else {
            this[newRowKey].account_id = '';
            this[newRowKey].name = '';
            this[newRowKey].amount = 0;
        }
    },

    async saveData(section) {
        let currentNewRow;
        
        switch(section) {
            case 'pendapatan':
                currentNewRow = this.newRowPendapatan;
                break;
            case 'hpp':
                currentNewRow = this.newRowHpp;
                break;
            case 'operasional':
                currentNewRow = this.newRowOperasional;
                break;
        }
        
        if (!this.validateForm(section)) return;

        try {
            const response = await fetch('/labarugi', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    type: section,
                    account_id: currentNewRow.account_id,
                    name: currentNewRow.name,
                    amount: currentNewRow.amount
                })
            });

            const data = await response.json();
            
            if (data.success) {
                const newRowData = { 
                    id: data.data.id,
                    account_id: currentNewRow.account_id,
                    name: currentNewRow.name,
                    amount: Number(data.data.balance) || 0,
                    isEditing: false
                };

                if (section === 'pendapatan') {
                    this.pendapatanRows.push(newRowData);
                } else if (section === 'hpp') {
                    this.hppRows.push(newRowData);
                } else {
                    this.operasionalRows.push(newRowData);
                }

                this.resetForm(section);
                
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Data berhasil disimpan',
                    icon: 'success',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                
                this.recalculateTotals();
            } else {
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Gagal menambahkan data: ' + data.message,
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            }
        } catch (error) {
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan: ' + error.message,
                icon: 'error',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        }
    },

    validateForm(section) {
        let currentNewRow;
        
        switch(section) {
            case 'pendapatan':
                currentNewRow = this.newRowPendapatan;
                break;
            case 'hpp':
                currentNewRow = this.newRowHpp;
                break;
            case 'operasional':
                currentNewRow = this.newRowOperasional;
                break;
        }
        
        if (!currentNewRow.account_id) {
            Swal.fire({
                title: 'Validasi Gagal!',
                text: 'Silakan pilih kode akun terlebih dahulu',
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
            return false;
        }
        return true;
    },

    resetForm(section) {
        switch(section) {
            case 'pendapatan':
                this.newRowPendapatan = { account_id: '', name: '', amount: 0 };
                break;
            case 'hpp':
                this.newRowHpp = { account_id: '', name: '', amount: 0 };
                break;
            case 'operasional':
                this.newRowOperasional = { account_id: '', name: '', amount: 0 };
                break;
        }
    },

    startEdit(row, section) {
        this.activeSection = section;
        row.originalData = { ...row };
        row.isEditing = true;
    },

    async saveEdit(row) {
        try {
            const response = await fetch(`/labarugi/${this.activeSection}/${row.id}`, {
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
                Object.assign(row, {
                    ...data.data,
                    amount: Number(data.data.balance) || 0
                });
                
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Data berhasil diupdate',
                    icon: 'success',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                
                this.recalculateTotals();
            } else {
                Swal.fire({
                    title: 'Gagal!',
                    text: data.message || 'Terjadi kesalahan saat menyimpan perubahan',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            }
        } catch (error) {
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan: ' + error.message,
                icon: 'error',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        }
    },

    cancelEdit(row) {
        if (row.originalData) {
            Object.assign(row, row.originalData);
            delete row.originalData;
        }
        row.isEditing = false;
    },

    async deleteRow(row, section) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Anda ingin menghapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/labarugi/${section}/${row.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        if (section === 'pendapatan') {
                            this.pendapatanRows = this.pendapatanRows.filter(r => r.id !== row.id);
                        } else if (section === 'hpp') {
                            this.hppRows = this.hppRows.filter(r => r.id !== row.id);
                        } else {
                            this.operasionalRows = this.operasionalRows.filter(r => r.id !== row.id);
                        }
                        
                        Swal.fire({
                            title: 'Terhapus!',
                            text: 'Data berhasil dihapus',
                            icon: 'success',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                        
                        this.recalculateTotals();
                    }
                } catch (error) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan: ' + error.message,
                        icon: 'error',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                }
            }
        });
    },

    getTotal(type) {
        const rows = type === 'pendapatan' ? this.pendapatanRows :
                    type === 'hpp' ? this.hppRows :
                    this.operasionalRows;
        return rows.reduce((sum, row) => sum + (Number(row.amount) || 0), 0);
    },

    getTotalPendapatan() {
        return this.getTotal('pendapatan');
    },

    getTotalHPP() {
        return this.getTotal('hpp');
    },

    getTotalOperasional() {
        return this.getTotal('operasional');
    },

    getLabaBersih() {
        return this.getTotalPendapatan() - (this.getTotalHPP() + this.getTotalOperasional());
    },

    recalculateTotals() {
        this.$nextTick(() => {
            this.getTotalPendapatan();
            this.getTotalHPP();
            this.getTotalOperasional();
            this.getLabaBersih();
        });
    },

    formatNumber(number) {
        return new Intl.NumberFormat('id-ID').format(Number(number) || 0);
    },

    getFilteredRows(rows) {
        return rows.filter(row => 
            (row.name?.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
            row.account_id?.toLowerCase().includes(this.searchTerm.toLowerCase()))
        );
    },

    init() {
        // Listen for balance updates
        document.addEventListener('balance-updated', () => {
            this.recalculateTotals();
        });
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

            <!-- Tabel Pendapatan -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4 text-blue-600">Pendapatan</h2>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-blue-600 text-white text-sm">
                                <th class="py-3 px-4 text-left border w-32">KODE AKUN</th>
                                <th class="py-3 px-4 text-left border">NAMA AKUN</th>
                                <th class="py-3 px-4 text-right border w-48">JUMLAH</th>
                                <th class="py-3 px-4 text-right border w-48">TOTAL</th>
                                <th class="py-3 px-4 text-center border w-32">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            <!-- Input Row untuk Pendapatan -->
                            <tr class="bg-gray-50">
                                <td class="py-2 px-4 border">
                                    <select x-model="newRowPendapatan.account_id" 
                                            @change="updateNewRowName($event.target.value, 'pendapatan')"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="">Pilih Kode Akun</option>
                                        <template x-for="account in availableAccounts" :key="account.account_id">
                                            <option :value="account.account_id" x-text="account.account_id"></option>
                                        </template>
                                    </select>
                                </td>
                                <td class="py-2 px-4 border">
                                    <input type="text" x-model="newRowPendapatan.name" readonly
                                           class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm">
                                </td>
                                <td class="py-2 px-4 border">
                                    <input type="text" x-model="newRowPendapatan.amount" readonly
                                           class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm text-right">
                                </td>
                                <td class="py-2 px-4 border"></td>
                                <td class="py-2 px-4 border text-center">
                                    <button @click="saveData('pendapatan')" 
                                            class="p-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>

                            <!-- Existing Rows untuk Pendapatan -->
                            <template x-for="row in getFilteredRows(pendapatanRows)" :key="row.id">
                                <tr :class="{'bg-blue-50': row.isEditing}" class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border" x-text="row.account_id"></td>
                                    <td class="py-2 px-4 border" x-text="row.name"></td>
                                    <td class="py-2 px-4 border text-right">
                                        <span class="balance-amount" 
                                              data-type="pendapatan"
                                              :data-account-id="row.account_id"
                                              x-text="formatNumber(row.amount)">
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 border"></td>
                                    <td class="py-2 px-4 border text-center">
                                        <template x-if="!row.isEditing">
                                            <div class="flex justify-center gap-2">
                                                <button @click="startEdit(row, 'pendapatan')" 
                                                        class="p-1 text-blue-600 hover:bg-blue-50 rounded">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <button @click="deleteRow(row, 'pendapatan')" 
                                                        class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="row.isEditing">
                                            <div class="flex justify-center gap-2">
                                                <button @click="saveEdit(row)" 
                                                        class="p-1 text-green-600 hover:bg-green-50 rounded">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                                <button @click="cancelEdit(row)" 
                                                        class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                    </td>
                                </tr>
                            </template>

                            <!-- Summary Row untuk Pendapatan -->
                            <tr class="bg-gray-50 text-black">
                                <td colspan="2" class="py-2 px-4 border font-medium text-center">
                                    TOTAL PENDAPATAN
                                </td>
                                <td class="py-2 px-4 border"></td>
                                <td class="py-2 px-4 border text-right font-medium" x-text="formatNumber(getTotalPendapatan())"></td>
                                <td class="py-2 px-4 border"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel HPP -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4 text-blue-600">Harga Pokok Penjualan (HPP)</h2>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-blue-600 text-white text-sm">
                                <th class="py-3 px-4 text-left border w-32">KODE AKUN</th>
                                <th class="py-3 px-4 text-left border">NAMA AKUN</th>
                                <th class="py-3 px-4 text-right border w-48">JUMLAH</th>
                                <th class="py-3 px-4 text-right border w-48">TOTAL</th>
                                <th class="py-3 px-4 text-center border w-32">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            <!-- Input Row untuk HPP -->
                            <tr class="bg-gray-50">
                                <td class="py-2 px-4 border">
                                    <select x-model="newRowHpp.account_id" 
                                            @change="updateNewRowName($event.target.value, 'hpp')"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="">Pilih Kode Akun</option>
                                        <template x-for="account in availableAccounts" :key="account.account_id">
                                            <option :value="account.account_id" x-text="account.account_id"></option>
                                        </template>
                                    </select>
                                </td>
                                <td class="py-2 px-4 border">
                                    <input type="text" x-model="newRowHpp.name" readonly
                                           class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm">
                                </td>
                                <td class="py-2 px-4 border">
                                    <input type="text" x-model="newRowHpp.amount" readonly
                                           class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm text-right">
                                </td>
                                <td class="py-2 px-4 border"></td>
                                <td class="py-2 px-4 border text-center">
                                    <button @click="saveData('hpp')" 
                                            class="p-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>

                            <!-- Existing Rows untuk HPP -->
                            <template x-for="row in getFilteredRows(hppRows)" :key="row.id">
                                <tr :class="{'bg-blue-50': row.isEditing}" class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border" x-text="row.account_id"></td>
                                    <td class="py-2 px-4 border" x-text="row.name"></td>
                                    <td class="py-2 px-4 border text-right">
                                        <span class="balance-amount" 
                                              data-type="hpp"
                                              :data-account-id="row.account_id"
                                              x-text="formatNumber(row.amount)">
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 border"></td>
                                    <td class="py-2 px-4 border text-center">
                                        <template x-if="!row.isEditing">
                                            <div class="flex justify-center gap-2">
                                                <button @click="startEdit(row, 'hpp')" 
                                                        class="p-1 text-blue-600 hover:bg-blue-50 rounded">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <button @click="deleteRow(row, 'hpp')" 
                                                        class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="row.isEditing">
                                            <div class="flex justify-center gap-2">
                                                <button @click="saveEdit(row)" 
                                                        class="p-1 text-green-600 hover:bg-green-50 rounded">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                                <button @click="cancelEdit(row)" 
                                                        class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                    </td>
                                </tr>
                            </template>

                            <!-- Summary Row untuk HPP -->
                            <tr class="bg-gray-50 text-black">
                                <td colspan="2" class="py-2 px-4 border font-medium text-center">
                                    TOTAL HPP
                                </td>
                                <td class="py-2 px-4 border"></td>
                                <td class="py-2 px-4 border text-right font-medium" x-text="formatNumber(getTotalHPP())"></td>
                                <td class="py-2 px-4 border"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel Biaya Operasional -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4 text-blue-600">Biaya Operasional</h2>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-blue-600 text-white text-sm">
                                <th class="py-3 px-4 text-left border w-32">KODE AKUN</th>
                                <th class="py-3 px-4 text-left border">NAMA AKUN</th>
                                <th class="py-3 px-4 text-right border w-48">JUMLAH</th>
                                <th class="py-3 px-4 text-right border w-48">TOTAL</th>
                                <th class="py-3 px-4 text-center border w-32">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            <!-- Input Row untuk Biaya Operasional -->
                            <tr class="bg-gray-50">
                                <td class="py-2 px-4 border">
                                    <select x-model="newRowOperasional.account_id" 
                                            @change="updateNewRowName($event.target.value, 'operasional')"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="">Pilih Kode Akun</option>
                                        <template x-for="account in availableAccounts" :key="account.account_id">
                                            <option :value="account.account_id" x-text="account.account_id"></option>
                                        </template>
                                    </select>
                                </td>
                                <td class="py-2 px-4 border">
                                    <input type="text" x-model="newRowOperasional.name" readonly
                                           class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm">
                                </td>
                                <td class="py-2 px-4 border">
                                    <input type="text" x-model="newRowOperasional.amount" readonly
                                           class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm text-right">
                                </td>
                                <td class="py-2 px-4 border"></td>
                                <td class="py-2 px-4 border text-center">
                                    <button @click="saveData('operasional')" 
                                            class="p-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>

                            <!-- Existing Rows untuk Biaya Operasional -->
                            <template x-for="row in getFilteredRows(operasionalRows)" :key="row.id">
                                <tr :class="{'bg-blue-50': row.isEditing}" class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border" x-text="row.account_id"></td>
                                    <td class="py-2 px-4 border" x-text="row.name"></td>
                                    <td class="py-2 px-4 border text-right">
                                        <span class="balance-amount" 
                                              data-type="operasional"
                                              :data-account-id="row.account_id"
                                              x-text="formatNumber(row.amount)">
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 border"></td>
                                    <td class="py-2 px-4 border text-center">
                                        <template x-if="!row.isEditing">
                                            <div class="flex justify-center gap-2">
                                                <button @click="startEdit(row, 'operasional')" 
                                                        class="p-1 text-blue-600 hover:bg-blue-50 rounded">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <button @click="deleteRow(row, 'operasional')" 
                                                        class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="row.isEditing">
                                            <div class="flex justify-center gap-2">
                                                <button @click="saveEdit(row)" 
                                                        class="p-1 text-green-600 hover:bg-green-50 rounded">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                                <button @click="cancelEdit(row)" 
                                                        class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                    </td>
                                </tr>
                            </template>

                            <!-- Summary Row untuk Biaya Operasional -->
                            <tr class="bg-gray-50 text-black">
                                <td colspan="2" class="py-2 px-4 border font-medium text-center">
                                    TOTAL BIAYA OPERASIONAL
                                </td>
                                <td class="py-2 px-4 border"></td>
                                <td class="py-2 px-4 border text-right font-medium" x-text="formatNumber(getTotalOperasional())"></td>
                                <td class="py-2 px-4 border"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Laba/Rugi Bersih -->
            <div class="mt-8 mb-8">
                <div class="flex flex-row gap-4">
                    <div class="w-full rounded-lg bg-blue-50 p-3 flex flex-row items-center justify-between">
                        <h2 class="text-base font-semibold text-blue-800 ml-4">LABA/RUGI BERSIH</h2>
                        <p class="text-lg font-bold mr-4" :class="getLabaBersih() >= 0 ? 'text-green-600' : 'text-red-600'" 
                        x-text="formatNumber(getLabaBersih())"></p>
                    </div>
                </div>
            </div>

            <!-- Print Button -->
            <div class="flex justify-between mt-6">
                <button onclick="window.location.href = '{{ route('pdf.laba-rugi') }}'"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Print PDF</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number || 0);
        }

        async function updateBalance(element) {
            const accountId = element.dataset.accountId;
            const type = element.dataset.type;
            try {
                const response = await fetch(`/labarugi/get-balance/${accountId}`);
                const data = await response.json();
                
                if(data.success) {
                    const row = element.closest('tr').__x.$data;
                    if (row && row.row) {
                        row.row.amount = data.balance;
                        element.textContent = formatNumber(data.balance);
                        
                        document.dispatchEvent(new CustomEvent('balance-updated'));
                    }
                }
            } catch(error) {
                console.error('Error fetching balance:', error);
            }
        }

        function updateAllBalances() {
            document.querySelectorAll('.balance-amount').forEach(updateBalance);
        }

        setInterval(updateAllBalances, 5000);
    });
</script>
</div>
@endsection