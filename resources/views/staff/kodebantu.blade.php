@extends('main')

@section('title', 'Kode Bantu')

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
<div class="bg-gray-50 min-h-screen flex flex-col" 
    x-data="{ 
    searchTerm: '',
    accounts: {{ Js::from($accounts) }},
    newRow: {
        helper_id: '',
        name: '',
        status: 'PIUTANG',
        balance: ''
    },
    validateForm(row) {
        if (!row.helper_id || !row.name) {
            Swal.fire({
                title: 'Perhatian',
                text: 'Kode dan Nama harus diisi',
                icon: 'warning',
                confirmButtonColor: '#3085d6'
            });
            return false;
        }
        // Set default value 0 if balance is empty
        if (!row.balance) {
            row.balance = 0;
        }
        return true;
    },
    async saveData(url = '{{ route('kodebantu.store') }}', method = 'POST', data = null) {
        const rowData = data || this.newRow;
        if (!this.validateForm(rowData)) return;

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(rowData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                if (method === 'POST') {
                    this.accounts.push({...result.account, isEditing: false});
                    this.newRow = {
                        helper_id: '',
                        name: '',
                        status: 'PIUTANG',
                        balance: ''
                    };
                }
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Data berhasil disimpan',
                    icon: 'success',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                Swal.fire({
                    title: 'Gagal!',
                    text: result.message || 'Terjadi kesalahan saat menyimpan data',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan saat menyimpan data: ' + error.message,
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
        }
    },
    async deleteAccount(accountId) {
        const result = await Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menghapus data ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        });
        
        if (!result.isConfirmed) return;
        
        try {
            const response = await fetch(`/kodebantu/${accountId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.accounts = this.accounts.filter(account => account.id !== accountId);
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Data berhasil dihapus',
                    icon: 'success',
                    confirmButtonColor: '#3085d6'
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan saat menghapus data',
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
        }
    },
    startEdit(account) {
        account.originalData = { ...account };
        account.isEditing = true;
    },
    async saveEdit(account) {
        if (!this.validateForm(account)) return;

        try {
            const response = await fetch(`/kodebantu/${account.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(account)
            });
            
            const result = await response.json();
            
            if (result.success) {
                account.isEditing = false;
                delete account.originalData;
                Object.assign(account, result.account);
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Data berhasil diperbarui',
                    icon: 'success',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                Swal.fire({
                    title: 'Gagal!',
                    text: result.message || 'Terjadi kesalahan saat menyimpan perubahan',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan saat menyimpan perubahan',
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
        }
    },
    cancelEdit(account) {
        if (account.originalData) {
            Object.assign(account, account.originalData);
            delete account.originalData;
        }
        account.isEditing = false;
    }
}">
    <div class="flex overflow-hidden">
        <x-side-bar-menu></x-side-bar-menu>
        <div id="main-content" class="relative text-black font-poppins w-full h-full overflow-y-auto">
            <x-nav-bar></x-nav-bar>

            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-black">Kode Bantu</h1>
                        <p class="text-sm text-gray-600 mt-1">Kelola daftar kode bantu perusahaan</p>
                    </div>
                    <div class="flex gap-3">
                        <div class="relative">
                            <input type="text" 
                                x-model="searchTerm"
                                placeholder="Cari kode atau nama..." 
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
                                <th class="py-3 px-4 text-left border-r w-32">KODE BANTU</th>
                                <th class="py-3 px-4 text-left border-r">NAMA</th>
                                <th class="py-3 px-4 text-left border-r w-32">STATUS</th>
                                <th class="py-3 px-4 text-right w-36 border-r">SALDO AWAL</th>
                                <th class="py-3 px-4 text-center w-24">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            <!-- New Row Input -->
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 px-4 border-r">
                                    <input type="text" x-model="newRow.helper_id" 
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm"
                                        placeholder="Kode">
                                </td>
                                <td class="py-2 px-4 border-r">
                                    <input type="text" x-model="newRow.name" 
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm"
                                        placeholder="Nama">
                                </td>
                                <td class="py-2 px-4 border-r">
                                    <select x-model="newRow.status" 
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm">
                                        <option value="PIUTANG">PIUTANG</option>
                                        <option value="HUTANG">HUTANG</option>
                                    </select>
                                </td>
                                <td class="py-2 px-4 border-r">
                                    <input type="number" x-model="newRow.balance" 
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm text-right"
                                        placeholder="0">
                                </td>
                                <td class="py-2 px-4 text-center">
                                    <button @click="saveData()"
                                        class="p-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>

                            <!-- Existing Rows -->
                            <template x-for="(account, index) in accounts.filter(a => 
                                a.helper_id.toString().includes(searchTerm.toLowerCase()) ||
                                a.name.toLowerCase().includes(searchTerm.toLowerCase())
                            )" :key="index">
                                <tr :class="{'bg-blue-50': account.isEditing}" class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-r">
                                        <template x-if="!account.isEditing">
                                            <span x-text="account.helper_id"></span>
                                        </template>
                                        <template x-if="account.isEditing">
                                            <input type="text" x-model="account.helper_id" 
                                                class="w-full px-2 py-1.5 border rounded text-sm">
                                        </template>
                                    </td>
                                    
                                    <td class="py-2 px-4 border-r">
                                        <template x-if="!account.isEditing">
                                            <span x-text="account.name"></span>
                                        </template>
                                        <template x-if="account.isEditing">
                                            <input type="text" x-model="account.name" 
                                                class="w-full px-2 py-1.5 border rounded text-sm">
                                        </template>
                                    </td>
                                    
                                    <td class="py-2 px-4 border-r">
                                        <template x-if="!account.isEditing">
                                            <span x-text="account.status"></span>
                                        </template>
                                        <template x-if="account.isEditing">
                                            <select x-model="account.status" 
                                                class="w-full px-2 py-1.5 border rounded text-sm">
                                                <option value="PIUTANG">PIUTANG</option>
                                                <option value="HUTANG">HUTANG</option>
                                            </select>
                                        </template>
                                    </td>
                                    
                                    <td class="py-2 px-4 border-r text-right">
                                        <template x-if="!account.isEditing">
                                            <span x-text="account.balance ? new Intl.NumberFormat('id-ID').format(account.balance) : '-'"></span>
                                        </template>
                                        <template x-if="account.isEditing">
                                            <input type="number" x-model="account.balance" 
                                                class="w-full px-2 py-1.5 border rounded text-sm text-right">
                                        </template>
                                    </td>
                                    
                                    <td class="py-2 px-4 text-center">
                                        <template x-if="!account.isEditing">
                                            <div class="flex justify-center gap-2">
                                                <button @click="startEdit(account)" 
                                                    class="p-1 text-blue-600 hover:bg-blue-50 rounded">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <button @click="deleteAccount(account.id)" 
                                                    class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="account.isEditing">
                                            <div class="flex justify-center gap-2">
                                                <button @click="saveEdit(account)" 
                                                    class="p-1 text-green-600 hover:bg-green-50 rounded">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                                <button @click="cancelEdit(account)" 
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
                        </tbody>
                        <tfoot class="bg-gray-50 text-sm">
                            <tr>
                                <td colspan="3" class="py-2 px-4 text-right font-medium border-r">Total Saldo Awal:</td>
                                <td class="py-2 px-4 text-right font-medium border-r" 
                                    x-text="'Rp.' + new Intl.NumberFormat('id-ID').format(accounts.reduce((sum, account) => sum + (Number(account.balance) || 0), 0))">
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-6">
                    <button onclick="window.location.href='{{ route('pdf.kode-bantu') }}'"
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