@extends('main')

@section('title', 'Jurnal Umum')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col" x-data="{ 
    searchTerm: '',
    transactions: {{ Js::from($journals) }},
    accounts: {{ Js::from($accounts) }},
    helpers: {{ Js::from($helpers) }},
    newRow: {
        date: '',
        transaction_proof: '',
        description: '',
        account_id: '',
        helper_id: '',
        debit: '',
        credit: ''
    },
    async saveData(url = '{{ route('jurnalumum.store') }}', method = 'POST', data = null) {
        if (!this.validateForm(data || this.newRow)) return;
        
        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data || this.newRow)
            });
            
            const result = await response.json();
            
            if (result.success) {
                if (method === 'POST') {
                    this.transactions.unshift({...result.journal, isEditing: false});
                    this.resetForm();
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
    
    validateForm(data) {
        if (!data.date || !data.description || !data.account_id) {
            alert('Tanggal, Keterangan, dan Akun harus diisi');
            return false;
        }
        if (!data.debit && !data.credit) {
            alert('Nilai Debet atau Kredit harus diisi');
            return false;
        }
        return true;
    },
    
    resetForm() {
        this.newRow = {
            date: '',
            transaction_proof: '',
            description: '',
            account_id: '',
            helper_id: '',
            debit: '',
            credit: ''
        };
    },
    
    async deleteTransaction(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) return;
        
        try {
            const response = await fetch(`/jurnalumum/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.transactions = this.transactions.filter(t => t.id !== id);
                alert('Data berhasil dihapus');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus data');
        }
    },
    
    startEdit(transaction) {
        transaction.originalData = { ...transaction };
        transaction.isEditing = true;
    },
    
    async saveEdit(transaction) {
        if (!this.validateForm(transaction)) return;

        try {
            const response = await fetch(`/jurnalumum/${transaction.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(transaction)
            });
            
            const result = await response.json();
            
            if (result.success) {
                transaction.isEditing = false;
                delete transaction.originalData;
                Object.assign(transaction, result.journal);
            } else {
                alert(result.message || 'Terjadi kesalahan saat menyimpan perubahan');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan perubahan');
        }
    },
    
    cancelEdit(transaction) {
        if (transaction.originalData) {
            Object.assign(transaction, transaction.originalData);
            delete transaction.originalData;
        }
        transaction.isEditing = false;
    },
    
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
        <x-side-bar-menu></x-side-bar-menu>
        <div id="main-content" class="relative text-black ml-72 font-poppins w-full h-full overflow-y-auto">
            <x-nav-bar></x-nav-bar>
            
            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-black">Jurnal Umum</h1>
                        <p class="text-sm text-gray-600 mt-1">Kelola jurnal umum perusahaan</p>
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
                                <th class="border border-blue-500 px-4 py-2 w-24">TANGGAL</th>
                                <th class="border border-blue-500 px-4 py-2 w-32">BUKTI TRANSAKSI</th>
                                <th class="border border-blue-500 px-4 py-2">KETERANGAN</th>
                                <th colspan="2" class="border border-blue-500 px-4 py-2">POS AKUN</th>
                                <th colspan="2" class="border border-blue-500 px-4 py-2">KODE BANTU</th>
                                <th class="border border-blue-500 px-4 py-2 w-32">DEBET</th>
                                <th class="border border-blue-500 px-4 py-2 w-32">KREDIT</th>
                                <th class="border border-blue-500 px-4 py-2 w-16">AKSI</th>
                            </tr>
                            <tr class="bg-blue-600 text-white text-sm">
                                <th colspan="3" class="border border-blue-500 px-4 py-2"></th>
                                <th class="border border-blue-500 px-4 py-2">NAMA AKUN</th>
                                <th class="border border-blue-500 px-4 py-2">KODE</th>
                                <th class="border border-blue-500 px-4 py-2">NAMA</th>
                                <th class="border border-blue-500 px-4 py-2">KODE</th>
                                <th colspan="3" class="border border-blue-500 px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- New Row Input -->
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 px-4 border-r">
                                    <input type="date" x-model="newRow.date" 
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm">
                                </td>
                                <td class="py-2 px-4 border-r">
                                    <input type="text" x-model="newRow.transaction_proof" 
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm"
                                        placeholder="Bukti Transaksi">
                                </td>
                                <td class="py-2 px-4 border-r">
                                    <input type="text" x-model="newRow.description" 
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm"
                                        placeholder="Keterangan">
                                </td>
                                <!-- POS AKUN -->
                                <td class="py-2 px-4 border-r">
                                    <select x-model="newRow.account_id" 
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm">
                                        <option value="">Pilih Akun</option>
                                        <template x-for="account in accounts" :key="account.account_id">
                                            <option :value="account.account_id" x-text="account.name"></option>
                                        </template>
                                    </select>
                                </td>
                                <td class="py-2 px-4 border-r">
                                    <input type="text" 
                                        :value="getAccountCode(newRow.account_id)"
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm bg-gray-100"
                                        readonly>
                                </td>

                                <!-- KODE BANTU -->
                                <td class="py-2 px-4 border-r">
                                    <select x-model="newRow.helper_id" 
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm">
                                        <option value="">Pilih Kode Bantu</option>
                                        <template x-for="helper in helpers" :key="helper.helper_id">
                                            <option :value="helper.helper_id" x-text="helper.name"></option>
                                        </template>
                                    </select>
                                </td>
                                <td class="py-2 px-4 border-r">
                                    <input type="text" 
                                        :value="getHelperCode(newRow.helper_id)"
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm bg-gray-100"
                                        readonly>
                                </td>
                                
                                <!-- Amount -->
                                <td class="py-2 px-4 border-r">
                                    <input type="number" x-model="newRow.debit" 
                                        class="w-full px-2 py-1.5 border rounded focus:ring-2 focus:ring-blue-500 text-sm text-right"
                                        placeholder="0">
                                </td>
                                <td class="py-2 px-4 border-r">
                                    <input type="number" x-model="newRow.credit" 
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
                            <template x-for="transaction in transactions.filter(t => 
                                t.description?.toLowerCase().includes(searchTerm.toLowerCase()) ||
                                t.transaction_proof?.toLowerCase().includes(searchTerm.toLowerCase()))"
                                :key="transaction.id">
                                <tr :class="{'bg-blue-50': transaction.isEditing}" class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-r">
                                        <template x-if="!transaction.isEditing">
                                            <span x-text="transaction.date"></span>
                                        </template>
                                        <template x-if="transaction.isEditing">
                                            <input type="date" x-model="transaction.date" 
                                                class="w-full px-2 py-1.5 border rounded text-sm">
                                        </template>
                                    </td>
                                    
                                    <td class="py-2 px-4 border-r">
                                        <template x-if="!transaction.isEditing">
                                            <span x-text="transaction.transaction_proof"></span>
                                        </template>
                                        <template x-if="transaction.isEditing">
                                            <input type="text" x-model="transaction.transaction_proof" 
                                                class="w-full px-2 py-1.5 border rounded text-sm">
                                        </template>
                                    </td>
                                    
                                    <td class="py-2 px-4 border-r">
                                        <template x-if="!transaction.isEditing">
                                            <span x-text="transaction.description"></span>
                                        </template>
                                        <template x-if="transaction.isEditing">
                                            <input type="text" x-model="transaction.description" 
                                                class="w-full px-2 py-1.5 border rounded text-sm">
                                        </template>
                                    </td>
                                    
                                    <td class="py-2 px-4 border-r">
                                        <template x-if="!transaction.isEditing">
                                            <span x-text="getAccountName(transaction.account_id)"></span>
                                        </template>
                                        <template x-if="transaction.isEditing">
                                            <select x-model="transaction.account_id" 
                                                class="w-full px-2 py-1.5 border rounded text-sm">
                                                <option value="">Pilih Akun</option>
                                                <template x-for="account in accounts" :key="account.account_id">
                                                    <option :value="account.account_id" x-text="account.name"></option>
                                                </template>
                                            </select>
                                        </template>
                                    </td>
                                    
                                    <td class="py-2 px-4 border-r">
                                        <template x-if="!transaction.isEditing">
                                            <span x-text="getAccountCode(transaction.account_id)"></span>
                                        </template>
                                        <template x-if="transaction.isEditing">
                                            <input type="text" 
                                                :value="getAccountCode(transaction.account_id)"
                                                class="w-full px-2 py-1.5 border rounded text-sm bg-gray-100"
                                                readonly>
                                        </template>
                                    </td>
                                    
                                    <td class="py-2 px-4 border-r">
                                        <template x-if="!transaction.isEditing">
                                            <span x-text="getHelperName(transaction.helper_id)"></span>
                                        </template>
                                        <template x-if="transaction.isEditing">
                                            <select x-model="transaction.helper_id" 
                                                class="w-full px-2 py-1.5 border rounded text-sm">
                                                <option value="">Pilih Kode Bantu</option>
                                                <template x-for="helper in helpers" :key="helper.helper_id">
                                                    <option :value="helper.helper_id" x-text="helper.name"></option>
                                                </template>
                                            </select>
                                        </template>
                                    </td>
                                    
                                    <td class="py-2 px-4 border-r">
                                        <template x-if="!transaction.isEditing">
                                            <span x-text="getHelperCode(transaction.helper_id)"></span>
                                        </template>
                                        <template x-if="transaction.isEditing">
                                            <input type="text" 
                                                :value="getHelperCode(transaction.helper_id)"
                                                class="w-full px-2 py-1.5 border rounded text-sm bg-gray-100"
                                                readonly>
                                        </template>
                                    </td>
                                    
                                    <td class="py-2 px-4 border-r text-right">
                                        <template x-if="!transaction.isEditing">
                                            <span x-text="transaction.debit ? new Intl.NumberFormat('id-ID').format(transaction.debit) : '-'"></span>
                                        </template>
                                        <template x-if="transaction.isEditing">
                                            <input type="number" x-model="transaction.debit" 
                                                class="w-full px-2 py-1.5 border rounded text-sm text-right">
                                        </template>
                                    </td>
                                    
                                    <td class="py-2 px-4 border-r text-right">
                                        <template x-if="!transaction.isEditing">
                                            <span x-text="transaction.credit ? new Intl.NumberFormat('id-ID').format(transaction.credit) : '-'"></span>
                                        </template>
                                        <template x-if="transaction.isEditing">
                                            <input type="number" x-model="transaction.credit" 
                                                class="w-full px-2 py-1.5 border rounded text-sm text-right">
                                        </template>
                                    </td>
                                    
                                    <td class="py-2 px-4 text-center">
                                        <template x-if="!transaction.isEditing">
                                            <div class="flex justify-center gap-2">
                                                <button @click="startEdit(transaction)" 
                                                    class="p-1 text-blue-600 hover:bg-blue-50 rounded">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <button @click="deleteTransaction(transaction.id)" 
                                                    class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="transaction.isEditing">
                                            <div class="flex justify-center gap-2">
                                                <button @click="saveEdit(transaction)" 
                                                    class="p-1 text-green-600 hover:bg-green-50 rounded">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                                <button @click="cancelEdit(transaction)" 
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
                                <td colspan="7" class="py-2 px-4 text-right font-medium border-r">Total:</td>
                                <td class="py-2 px-4 text-right font-medium border-r" 
                                    x-text="new Intl.NumberFormat('id-ID').format(
                                        transactions.reduce((sum, t) => sum + (parseFloat(t.debit) || 0), 0)
                                    )">
                                </td>
                                <td class="py-2 px-4 text-right font-medium border-r"
                                    x-text="new Intl.NumberFormat('id-ID').format(
                                        transactions.reduce((sum, t) => sum + (parseFloat(t.credit) || 0), 0)
                                    )">
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-6">
                    <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2">
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