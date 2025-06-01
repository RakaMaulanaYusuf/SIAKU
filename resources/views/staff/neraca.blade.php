@extends('main')

@section('title', 'Neraca')

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
    aktivalancarRows: {{ Js::from($aktivalancar->map(function($item) {
        return array_merge($item, ['amount' => $item['balance']]);
    })) }},
    aktivatetapRows: {{ Js::from($aktivatetap->map(function($item) {
        return array_merge($item, ['amount' => $item['balance']]);
    })) }},
    kewajibanRows: {{ Js::from($kewajiban->map(function($item) {
        return array_merge($item, ['amount' => $item['balance']]);
    })) }},
    ekuitasRows: {{ Js::from($ekuitas->map(function($item) {
        return array_merge($item, ['amount' => $item['balance']]);
    })) }},
    availableAccounts: {{ Js::from($availableAccounts) }},
    searchTerm: '',
    
    // Objek newRow terpisah untuk setiap tabel
    newRowAktivaLancar: {
        account_id: '',
        name: '',
        amount: 0
    },
    newRowAktivaTetap: {
        account_id: '',
        name: '',
        amount: 0
    },
    newRowKewajiban: {
        account_id: '',
        name: '',
        amount: 0
    },
    newRowEkuitas: {
        account_id: '',
        name: '',
        amount: 0
    },

    updateNewRowName(accountId, type) {
        const account = this.availableAccounts.find(acc => acc.account_id === accountId);
        let newRowKey;
        
        switch(type) {
            case 'aktivalancar':
                newRowKey = 'newRowAktivaLancar';
                break;
            case 'aktivatetap':
                newRowKey = 'newRowAktivaTetap';
                break;
            case 'kewajiban':
                newRowKey = 'newRowKewajiban';
                break;
            case 'ekuitas':
                newRowKey = 'newRowEkuitas';
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

    async saveData(type) {
        let currentNewRow;
        
        switch(type) {
            case 'aktivalancar':
                currentNewRow = this.newRowAktivaLancar;
                break;
            case 'aktivatetap':
                currentNewRow = this.newRowAktivaTetap;
                break;
            case 'kewajiban':
                currentNewRow = this.newRowKewajiban;
                break;
            case 'ekuitas':
                currentNewRow = this.newRowEkuitas;
                break;
        }
        
        if (!this.validateForm(type)) return;

        try {
            const response = await fetch('/neraca', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    type: type,
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

                if (type === 'aktivalancar') {
                    this.aktivalancarRows.push(newRowData);
                } else if (type === 'aktivatetap') {
                    this.aktivatetapRows.push(newRowData);
                } else if (type === 'kewajiban') {
                    this.kewajibanRows.push(newRowData);
                } else if (type === 'ekuitas') {
                    this.ekuitasRows.push(newRowData);
                }

                this.resetForm(type);
                
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

    validateForm(type) {
        let currentNewRow;
        
        switch(type) {
            case 'aktivalancar':
                currentNewRow = this.newRowAktivaLancar;
                break;
            case 'aktivatetap':
                currentNewRow = this.newRowAktivaTetap;
                break;
            case 'kewajiban':
                currentNewRow = this.newRowKewajiban;
                break;
            case 'ekuitas':
                currentNewRow = this.newRowEkuitas;
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

    resetForm(type) {
        switch(type) {
            case 'aktivalancar':
                this.newRowAktivaLancar = { account_id: '', name: '', amount: 0 };
                break;
            case 'aktivatetap':
                this.newRowAktivaTetap = { account_id: '', name: '', amount: 0 };
                break;
            case 'kewajiban':
                this.newRowKewajiban = { account_id: '', name: '', amount: 0 };
                break;
            case 'ekuitas':
                this.newRowEkuitas = { account_id: '', name: '', amount: 0 };
                break;
        }
    },

    startEdit(row) {
        row.originalData = { ...row };
        row.isEditing = true;
    },

    async saveEdit(row, type) {
        try {
            const response = await fetch(`/neraca/${type}/${row.id}`, {
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

    async deleteRow(row, type) {
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
                    const response = await fetch(`/neraca/${type}/${row.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        if (type === 'aktivalancar') {
                            this.aktivalancarRows = this.aktivalancarRows.filter(r => r.id !== row.id);
                        } else if (type === 'aktivatetap') {
                            this.aktivatetapRows = this.aktivatetapRows.filter(r => r.id !== row.id);
                        } else if (type === 'kewajiban') {
                            this.kewajibanRows = this.kewajibanRows.filter(r => r.id !== row.id);
                        } else if (type === 'ekuitas') {
                            this.ekuitasRows = this.ekuitasRows.filter(r => r.id !== row.id);
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
        return this[type + 'Rows'].reduce((sum, row) => {
            return sum + (Number(row.amount) || 0);
        }, 0);
    },

    getTotalAktiva() {
        const totalAktivaLancar = this.getTotal('aktivalancar');
        const totalAktivaTetap = this.getTotal('aktivatetap');
        return totalAktivaLancar + totalAktivaTetap;
    },

    getTotalPassiva() {
        const totalKewajiban = this.getTotal('kewajiban');
        const totalEkuitas = this.getTotal('ekuitas');
        return totalKewajiban + totalEkuitas;
    },

    recalculateTotals() {
        this.$nextTick(() => {
            // Force Alpine to recalculate all totals
            this.getTotal('aktivalancar');
            this.getTotal('aktivatetap');
            this.getTotal('kewajiban');
            this.getTotal('ekuitas');
            this.getTotalAktiva();
            this.getTotalPassiva();
        });
    },

    formatNumber(number) {
        return new Intl.NumberFormat('id-ID').format(Number(number) || 0);
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
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-black">Neraca</h1>
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

            <!-- Balance Status Alert -->
            <div x-show="getTotalAktiva() !== getTotalPassiva()" class="mb-6">
                <div class="bg-red-100 border-l-4 border-red-400 text-red-700 p-4 mb-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm leading-5 font-medium">Neraca Tidak Seimbang!</p>
                            <p class="text-sm leading-5 mt-1">
                                Total Aktiva: <span class="font-semibold" x-text="formatNumber(getTotalAktiva())"></span> | 
                                Total Pasiva: <span class="font-semibold" x-text="formatNumber(getTotalPassiva())"></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Balance Success Alert -->
            <div x-show="getTotalAktiva() === getTotalPassiva() && (getTotalAktiva() > 0 || getTotalPassiva() > 0)" class="mb-6">
                <div class="bg-green-100 border-l-4 border-green-400 text-green-700 p-4 mb-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm leading-5 font-medium">Neraca Seimbang!</p>
                            <p class="text-sm leading-5 mt-1">
                                Total Aktiva: <span class="font-semibold" x-text="formatNumber(getTotalAktiva())"></span> | 
                                Total Pasiva: <span class="font-semibold" x-text="formatNumber(getTotalPassiva())"></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-2 gap-6">
                <!-- Kolom Kiri (Aktiva) -->
                <div>
                    <!-- Aktiva Lancar -->
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-4">Aktiva Lancar</h2>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-blue-600 text-white text-sm">
                                        <th class="py-3 px-4 text-left border w-32">KODE AKUN</th>
                                        <th class="py-3 px-4 text-left border">NAMA AKUN</th>
                                        <th class="py-3 px-4 text-right border w-48">JUMLAH</th>
                                        <th class="py-3 px-4 text-center border w-24">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                                    <!-- Input Row Aktiva Lancar -->
                                    <tr class="bg-gray-50">
                                        <td class="py-2 px-4 border">
                                            <select x-model="newRowAktivaLancar.account_id" 
                                                    @change="updateNewRowName($event.target.value, 'aktivalancar')"
                                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
                                                <option value="">Pilih Kode Akun</option>
                                                <template x-for="account in availableAccounts" :key="account.account_id">
                                                    <option :value="account.account_id" x-text="account.account_id"></option>
                                                </template>
                                            </select>
                                        </td>
                                        <td class="py-2 px-4 border">
                                            <input type="text" x-model="newRowAktivaLancar.name" readonly
                                                class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm">
                                        </td>
                                        <td class="py-2 px-4 border">
                                            <input type="text" x-model="newRowAktivaLancar.amount" readonly
                                                class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm text-right">
                                        </td>
                                        <td class="py-2 px-4 border text-center">
                                            <button @click="saveData('aktivalancar')" 
                                                    class="p-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Existing Rows Aktiva Lancar -->
                                    <template x-for="row in aktivalancarRows" :key="row.id">
                                        <tr :class="{'bg-blue-50': row.isEditing}" class="hover:bg-gray-50">
                                            <td class="py-2 px-4 border" x-text="row.account_id"></td>
                                            <td class="py-2 px-4 border" x-text="row.name"></td>
                                            <td class="py-2 px-4 border text-right">
                                                <span class="balance-amount" 
                                                    :data-account-id="row.account_id"
                                                    data-type="aktivalancar"
                                                    x-text="formatNumber(row.amount)">
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border text-center">
                                                <div class="flex justify-center gap-2">
                                                    <button @click="deleteRow(row, 'aktivalancar')" 
                                                            class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>

                                    <!-- Total Aktiva Lancar -->
                                    <tr class="bg-gray-100 font-semibold">
                                        <td colspan="2" class="py-2 px-4 border text-center">TOTAL AKTIVA LANCAR</td>
                                        <td class="py-2 px-4 border text-right" x-text="formatNumber(getTotal('aktivalancar'))"></td>
                                        <td class="py-2 px-4 border"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Aktiva Tetap -->
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-4">Aktiva Tetap</h2>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-blue-600 text-white text-sm">
                                        <th class="py-3 px-4 text-left border w-32">KODE AKUN</th>
                                        <th class="py-3 px-4 text-left border">NAMA AKUN</th>
                                        <th class="py-3 px-4 text-right border w-48">JUMLAH</th>
                                        <th class="py-3 px-4 text-center border w-24">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                                    <!-- Input Row Aktiva Tetap -->
                                    <tr class="bg-gray-50">
                                        <td class="py-2 px-4 border">
                                            <select x-model="newRowAktivaTetap.account_id" 
                                                    @change="updateNewRowName($event.target.value, 'aktivatetap')"
                                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
                                                <option value="">Pilih Kode Akun</option>
                                                <template x-for="account in availableAccounts" :key="account.account_id">
                                                    <option :value="account.account_id" x-text="account.account_id"></option>
                                                </template>
                                            </select>
                                        </td>
                                        <td class="py-2 px-4 border">
                                            <input type="text" x-model="newRowAktivaTetap.name" readonly
                                                class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm">
                                        </td>
                                        <td class="py-2 px-4 border">
                                            <input type="text" x-model="newRowAktivaTetap.amount" readonly
                                                class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm text-right">
                                        </td>
                                        <td class="py-2 px-4 border text-center">
                                            <button @click="saveData('aktivatetap')" 
                                                    class="p-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Existing Rows Aktiva Tetap -->
                                    <template x-for="row in aktivatetapRows" :key="row.id">
                                        <tr :class="{'bg-blue-50': row.isEditing}" class="hover:bg-gray-50">
                                            <td class="py-2 px-4 border" x-text="row.account_id"></td>
                                            <td class="py-2 px-4 border" x-text="row.name"></td>
                                            <td class="py-2 px-4 border text-right">
                                                <span class="balance-amount" 
                                                    :data-account-id="row.account_id"
                                                    data-type="aktivatetap"
                                                    x-text="formatNumber(row.amount)">
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border text-center">
                                                <div class="flex justify-center gap-2">
                                                    <button @click="deleteRow(row, 'aktivatetap')" 
                                                            class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>

                                    <!-- Total Aktiva Tetap -->
                                    <tr class="bg-gray-100 font-semibold">
                                        <td colspan="2" class="py-2 px-4 border text-center">TOTAL AKTIVA TETAP</td>
                                        <td class="py-2 px-4 border text-right" x-text="formatNumber(getTotal('aktivatetap'))"></td>
                                        <td class="py-2 px-4 border"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Total Aktiva -->
                    <div class="bg-blue-100 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-blue-800">TOTAL AKTIVA</span>
                            <span class="font-bold text-blue-800" x-text="formatNumber(getTotalAktiva())"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Kolom Kanan (Pasiva) -->
                <div>
                    <!-- Kewajiban -->
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-4">Kewajiban</h2>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-blue-600 text-white text-sm">
                                        <th class="py-3 px-4 text-left border w-32">KODE AKUN</th>
                                        <th class="py-3 px-4 text-left border">NAMA AKUN</th>
                                        <th class="py-3 px-4 text-right border w-48">JUMLAH</th>
                                        <th class="py-3 px-4 text-center border w-24">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                                    <!-- Input Row Kewajiban -->
                                    <tr class="bg-gray-50">
                                        <td class="py-2 px-4 border">
                                            <select x-model="newRowKewajiban.account_id" 
                                                    @change="updateNewRowName($event.target.value, 'kewajiban')"
                                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
                                                <option value="">Pilih Kode Akun</option>
                                                <template x-for="account in availableAccounts" :key="account.account_id">
                                                    <option :value="account.account_id" x-text="account.account_id"></option>
                                                </template>
                                            </select>
                                        </td>
                                        <td class="py-2 px-4 border">
                                            <input type="text" x-model="newRowKewajiban.name" readonly
                                                class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm">
                                        </td>
                                        <td class="py-2 px-4 border">
                                            <input type="text" x-model="newRowKewajiban.amount" readonly
                                                class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm text-right">
                                        </td>
                                        <td class="py-2 px-4 border text-center">
                                            <button @click="saveData('kewajiban')" 
                                                    class="p-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Existing Rows Kewajiban -->
                                    <template x-for="row in kewajibanRows" :key="row.id">
                                        <tr :class="{'bg-blue-50': row.isEditing}" class="hover:bg-gray-50">
                                            <td class="py-2 px-4 border" x-text="row.account_id"></td>
                                            <td class="py-2 px-4 border" x-text="row.name"></td>
                                            <td class="py-2 px-4 border text-right">
                                                <span class="balance-amount" 
                                                    :data-account-id="row.account_id"
                                                    data-type="kewajiban"
                                                    x-text="formatNumber(row.amount)">
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border text-center">
                                                <div class="flex justify-center gap-2">
                                                    <button @click="deleteRow(row, 'kewajiban')" 
                                                            class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>

                                    <!-- Total Kewajiban -->
                                    <tr class="bg-gray-100 font-semibold">
                                        <td colspan="2" class="py-2 px-4 border text-center">TOTAL KEWAJIBAN</td>
                                        <td class="py-2 px-4 border text-right" x-text="formatNumber(getTotal('kewajiban'))"></td>
                                        <td class="py-2 px-4 border"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Ekuitas -->
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-4">Ekuitas</h2>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-blue-600 text-white text-sm">
                                        <th class="py-3 px-4 text-left border w-32">KODE AKUN</th>
                                        <th class="py-3 px-4 text-left border">NAMA AKUN</th>
                                        <th class="py-3 px-4 text-right border w-48">JUMLAH</th>
                                        <th class="py-3 px-4 text-center border w-24">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                                    <!-- Input Row Ekuitas -->
                                    <tr class="bg-gray-50">
                                        <td class="py-2 px-4 border">
                                            <select x-model="newRowEkuitas.account_id" 
                                                    @change="updateNewRowName($event.target.value, 'ekuitas')"
                                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
                                                <option value="">Pilih Kode Akun</option>
                                                <template x-for="account in availableAccounts" :key="account.account_id">
                                                    <option :value="account.account_id" x-text="account.account_id"></option>
                                                </template>
                                            </select>
                                        </td>
                                        <td class="py-2 px-4 border">
                                            <input type="text" x-model="newRowEkuitas.name" readonly
                                                class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm">
                                        </td>
                                        <td class="py-2 px-4 border">
                                            <input type="text" x-model="newRowEkuitas.amount" readonly
                                                class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm text-right">
                                        </td>
                                        <td class="py-2 px-4 border text-center">
                                            <button @click="saveData('ekuitas')" 
                                                    class="p-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Existing Rows Ekuitas -->
                                    <template x-for="row in ekuitasRows" :key="row.id">
                                        <tr :class="{'bg-blue-50': row.isEditing}" class="hover:bg-gray-50">
                                            <td class="py-2 px-4 border" x-text="row.account_id"></td>
                                            <td class="py-2 px-4 border" x-text="row.name"></td>
                                            <td class="py-2 px-4 border text-right">
                                                <span class="balance-amount" 
                                                    :data-account-id="row.account_id"
                                                    data-type="ekuitas"
                                                    x-text="formatNumber(row.amount)">
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border text-center">
                                                <div class="flex justify-center gap-2">
                                                    <button @click="deleteRow(row, 'ekuitas')" 
                                                            class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>

                                    <!-- Total Ekuitas -->
                                    <tr class="bg-gray-100 font-semibold">
                                        <td colspan="2" class="py-2 px-4 border text-center">TOTAL EKUITAS</td>
                                        <td class="py-2 px-4 border text-right" x-text="formatNumber(getTotal('ekuitas'))"></td>
                                        <td class="py-2 px-4 border"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Total Pasiva -->
                    <div class="bg-blue-100 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-blue-800">TOTAL PASIVA</span>
                            <span class="font-bold text-blue-800" x-text="formatNumber(getTotalPassiva())"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Print Button -->
            <div class="flex justify-end mt-6">
                <button @click="window.location.href = '/neraca/pdf'"
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
            try {
                const response = await fetch(`/neraca/get-balance/${accountId}`);
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
@endsection