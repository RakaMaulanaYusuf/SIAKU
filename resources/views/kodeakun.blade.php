@extends('main')

@section('title', 'Kode Akun')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col" 
    x-data="{ 
    searchTerm: '',
    accounts: {{ Js::from($accounts) }},
    newRow: {
        account_id: '',
        name: '',
        helper_table: '',
        balance_type: 'DEBIT',
        report_type: 'NERACA',
        debit: '',
        credit: ''
    },
    handleBalanceTypeChange(row) {
        if (row.balance_type === 'DEBIT') {
            row.credit = '';
        } else {
            row.debit = '';
        }
    },
    validateForm(row) {
        if (!row.account_id || !row.name) {
            alert('Kode dan Nama Akun harus diisi');
            return false;
        }
        if (row.balance_type === 'DEBIT' && row.credit) {
            alert('Kolom kredit harus kosong ketika pos saldo DEBIT');
            return false;
        }
        if (row.balance_type === 'CREDIT' && row.debit) {
            alert('Kolom debit harus kosong ketika pos saldo CREDIT');
            return false;
        }
        // Set default value 0 if empty
        if (row.balance_type === 'DEBIT') {
            row.debit = row.debit || 0;
        }
        if (row.balance_type === 'CREDIT') {
            row.credit = row.credit || 0;
        }
        return true;
    },
    async saveData(url = '{{ route('kodeakun.store') }}', method = 'POST', data = null) {
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
                        account_id: '',
                        name: '',
                        helper_table: '',
                        balance_type: 'DEBIT',
                        report_type: 'NERACA',
                        debit: '',
                        credit: ''
                    };
                }
                alert('Data berhasil disimpan');
            } else {
                alert(result.message || 'Terjadi kesalahan saat menyimpan data');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan data: ' + error.message);
        }
    },
    async deleteAccount(accountId) {
        if (!confirm('Apakah Anda yakin ingin menghapus akun ini?')) return;
        
        try {
            const response = await fetch(`/kodeakun/${accountId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.accounts = this.accounts.filter(account => account.id !== accountId);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus data');
        }
    },
    startEdit(account) {
        account.originalData = { ...account };
        account.isEditing = true;
    },
    async saveEdit(account) {
        if (!this.validateForm(account)) return;

        try {
            const response = await fetch(`/kodeakun/${account.id}`, {
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
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan perubahan');
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
            {{-- <x-side-bar-menu></x-side-bar-menu> --}}
            <x-nav-bar></x-nav-bar>
            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-black">Kode Akun</h1>
                        <p class="text-sm text-gray-600 mt-1">Kelola daftar kode akun perusahaan</p>
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
                                <th class="py-3 px-4 text-center border-b" colspan="3">SALDO AWAL</th>
                            </tr>
                            <tr class="bg-blue-600 text-white text-sm">
                                <th class="py-3 px-4 border-r" colspan="5"></th>
                                <th class="py-3 px-4 text-center border-r w-36">DEBIT</th>
                                <th class="py-3 px-4 text-center border-r w-36">CREDIT</th>
                                <th class="py-3 px-4 text-center w-16">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            <!-- New Row Input -->
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 px-4 border-r">
                                    <input type="text" x-model="newRow.account_id" 
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm"
                                        placeholder="Kode"
                                        style="width: 60px;">
                                </td>
                                <td class="py-2 px-4 border-r">
                                    <input type="text" x-model="newRow.name" 
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm"
                                        placeholder="Nama Akun">
                                </td>
                                <td class="py-2 px-4 border-r">
                                    <input type="text" x-model="newRow.helper_table" 
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm"
                                        placeholder="Tabel"
                                        style="width: 60px;">
                                </td>
                                <td class="py-2 px-4 border-r">
                                    <select x-model="newRow.balance_type" 
                                        @change="handleBalanceTypeChange(newRow)"
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm">
                                        <option value="DEBIT">DEBIT</option>
                                        <option value="CREDIT">CREDIT</option>
                                    </select>
                                </td>
                                <td class="py-2 px-4 border-r">
                                    <select x-model="newRow.report_type" 
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm">
                                        <option value="NERACA">NERACA</option>
                                        <option value="LABARUGI">LABA RUGI</option>
                                    </select>
                                </td>
                                <td class="py-2 px-4 border-r">
                                    <input type="number" x-model="newRow.debit" 
                                        :disabled="newRow.balance_type === 'CREDIT'"
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm text-right"
                                        :class="{'bg-gray-100': newRow.balance_type === 'CREDIT'}"
                                        placeholder="0">
                                </td>
                                <td class="py-2 px-4 border-r">
                                    <input type="number" x-model="newRow.credit" 
                                        :disabled="newRow.balance_type === 'DEBIT'"
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm text-right"
                                        :class="{'bg-gray-100': newRow.balance_type === 'DEBIT'}"
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
                                a.account_id.toString().includes(searchTerm.toLowerCase()) ||
                                a.name.toLowerCase().includes(searchTerm.toLowerCase())
                            )" :key="index">
                                <tr :class="{'bg-blue-50': account.isEditing}" class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-r">
                                        <template x-if="!account.isEditing">
                                            <span x-text="account.account_id"></span>
                                        </template>
                                        <template x-if="account.isEditing">
                                            <input type="text" x-model="account.account_id" 
                                                class="w-full px-2 py-1.5 border rounded text-sm"
                                                style="width: 60px;">
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
                                            <span x-text="account.helper_table"></span>
                                        </template>
                                        <template x-if="account.isEditing">
                                            <input type="text" x-model="account.helper_table" 
                                                class="w-full px-2 py-1.5 border rounded text-sm"
                                                style="width: 60px;">
                                        </template>
                                    </td>
                                    
                                    <td class="py-2 px-4 border-r">
                                        <template x-if="!account.isEditing">
                                            <span x-text="account.balance_type"></span>
                                        </template>
                                        <template x-if="account.isEditing">
                                            <select x-model="account.balance_type" 
                                                @change="handleBalanceTypeChange(account)"
                                                class="w-full px-2 py-1.5 border rounded text-sm">
                                                <option value="DEBIT">DEBIT</option>
                                                <option value="CREDIT">CREDIT</option>
                                            </select>
                                        </template>
                                    </td>
                                    
                                    <td class="py-2 px-4 border-r">
                                        <template x-if="!account.isEditing">
                                            <span x-text="account.report_type"></span>
                                        </template>
                                        <template x-if="account.isEditing">
                                            <select x-model="account.report_type" 
                                                class="w-full px-2 py-1.5 border rounded text-sm">
                                                <option value="NERACA">NERACA</option>
                                                <option value="LABARUGI">LABA RUGI</option>
                                            </select>
                                        </template>
                                    </td>
                                    
                                    <td class="py-2 px-4 border-r text-right">
                                        <template x-if="!account.isEditing">
                                            <span x-text="account.debit ? new Intl.NumberFormat('id-ID').format(account.debit) : '-'"></span>
                                        </template>
                                        <template x-if="account.isEditing">
                                            <input type="number" x-model="account.debit" 
                                                :disabled="account.balance_type === 'CREDIT'"
                                                :class="{'bg-gray-100': account.balance_type === 'CREDIT'}"
                                                class="w-full px-2 py-1.5 border rounded text-sm text-right">
                                        </template>
                                    </td>

                                    <td class="py-2 px-4 border-r text-right">
                                        <template x-if="!account.isEditing">
                                            <span x-text="account.credit ? new Intl.NumberFormat('id-ID').format(account.credit) : '-'"></span>
                                        </template>
                                        <template x-if="account.isEditing">
                                            <input type="number" x-model="account.credit" 
                                                :disabled="account.balance_type === 'DEBIT'"
                                                :class="{'bg-gray-100': account.balance_type === 'DEBIT'}"
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
                        <!-- Footer with Totals -->
                        <tfoot class="bg-gray-50 text-sm">
                            <tr>
                                <td colspan="5" class="py-2 px-4 text-right font-medium border-r">Total Saldo Awal:</td>
                                <td class="py-2 px-4 text-right font-medium border-r" 
                                    x-text="'Rp.' + new Intl.NumberFormat('id-ID').format(accounts.reduce((sum, account) => sum + (Number(account.debit) || 0), 0))">
                                </td>
                                <td class="py-2 px-4 text-right font-medium border-r"
                                    x-text="'Rp.' + new Intl.NumberFormat('id-ID').format(accounts.reduce((sum, account) => sum + (Number(account.credit) || 0), 0))">
                                </td>
                                <td class="py-2 px-4"></td>
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