@extends('main')

@section('title', 'HPP')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col" x-data="{ 
    rows: JSON.parse(localStorage.getItem('hppRows') || '[]'),
    newRow: {
        code: '',
        name: '',
        amount: '',
        isEditing: false
    },
    tempRow: {},
    init() {
        if (this.rows.length === 0) {
            this.rows = [
                { code: '51', name: 'Beban Gaji', amount: '40', isEditing: false },
                { code: '52', name: 'Beban Bensin', amount: '75', isEditing: false },
                { code: '53', name: 'Beban Makan dan Minum', amount: '50', isEditing: false },
                { code: '54', name: 'Beban Perawatan', amount: '15', isEditing: false }
            ];
            this.saveData();
        }
    },
    saveData() {
        localStorage.setItem('hppRows', JSON.stringify(this.rows));
        localStorage.setItem('totalHPP', this.getTotal());
    },
    getTotal() {
        return this.rows.reduce((sum, row) => sum + (Number(row.amount) || 0), 0);
    },
    formatNumber(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    },
    startEdit(row) {
        this.tempRow = { ...row };
        row.isEditing = true;
    },
    cancelEdit(row) {
        Object.assign(row, this.tempRow);
        row.isEditing = false;
    },
    saveEdit(row) {
        row.isEditing = false;
        this.saveData();
    }
}">
    <div class="flex overflow-hidden">
        <x-side-bar-menu></x-side-bar-menu>
        <div id="main-content" class="relative text-black ml-72 font-poppins w-full h-full overflow-y-auto">
            <!-- Current Company Indicator -->
            <div class="sticky top-0 bg-blue-600 text-white py-2 px-6 flex justify-between items-center z-50">
                <div class="flex items-center">
                    <span class="font-medium">Perusahaan Aktif:</span>
                    <span class="ml-2">PT Abadi Jaya</span>
                </div>
                <button class="text-sm bg-blue-700 px-3 py-1 rounded hover:bg-blue-800 transition-colors">
                    Ganti Perusahaan
                </button>
            </div>

            <!-- Header and Table Box -->
            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-black">HPP (Harga Pokok Penjualan)</h1>
                        <p class="text-sm text-gray-600 mt-1">31 Juni 2018</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('pendapatan') }}" 
                           class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-300">
                            Kembali ke Pendapatan
                        </a>
                        <button @click="saveData" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300">
                            Simpan Data
                        </button>
                        <a href="{{ route('biayaoperasional') }}" 
                           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-300">
                            Lanjut ke Biaya Operasional
                        </a>
                    </div>
                </div>

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
                            <!-- New Row Input -->
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 px-4 border">
                                    <input type="text" x-model="newRow.code"
                                        class="w-full px-2 py-1.5 border rounded text-sm"
                                        placeholder="Kode">
                                </td>
                                <td class="py-2 px-4 border">
                                    <input type="text" x-model="newRow.name"
                                        class="w-full px-2 py-1.5 border rounded text-sm"
                                        placeholder="Nama Akun">
                                </td>
                                <td class="py-2 px-4 border">
                                    <input type="number" x-model="newRow.amount"
                                        class="w-full px-2 py-1.5 border rounded text-sm text-right"
                                        placeholder="0">
                                </td>
                                <td class="py-2 px-4 border"></td>
                                <td class="py-2 px-4 border text-center">
                                    <button @click="
                                        if(newRow.code && newRow.name && newRow.amount) {
                                            rows.push({...newRow, isEditing: false});
                                            newRow = {
                                                code: '',
                                                name: '',
                                                amount: '',
                                                isEditing: false
                                            };
                                            saveData();
                                        }"
                                        class="p-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center justify-center w-full gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                        </svg>
                                        <span>Tambah</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Existing Rows -->
                            <template x-for="(row, index) in rows" :key="index">
                                <tr :class="{'bg-blue-50': row.isEditing}" class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border">
                                        <template x-if="!row.isEditing">
                                            <span x-text="row.code"></span>
                                        </template>
                                        <template x-if="row.isEditing">
                                            <input type="text" x-model="row.code"
                                                class="w-full px-2 py-1.5 border rounded text-sm">
                                        </template>
                                    </td>
                                    <td class="py-2 px-4 border">
                                        <template x-if="!row.isEditing">
                                            <span x-text="row.name"></span>
                                        </template>
                                        <template x-if="row.isEditing">
                                            <input type="text" x-model="row.name"
                                                class="w-full px-2 py-1.5 border rounded text-sm">
                                        </template>
                                    </td>
                                    <td class="py-2 px-4 border text-right">
                                        <template x-if="!row.isEditing">
                                            <span x-text="formatNumber(row.amount)"></span>
                                        </template>
                                        <template x-if="row.isEditing">
                                            <input type="number" x-model="row.amount"
                                                class="w-full px-2 py-1.5 border rounded text-sm text-right">
                                        </template>
                                    </td>
                                    <td class="py-2 px-4 border"></td>
                                    <td class="py-2 px-4 border text-center">
                                        <template x-if="!row.isEditing">
                                            <div class="flex justify-center gap-2">
                                                <button @click="startEdit(row)" 
                                                    class="p-1 text-blue-600 hover:bg-blue-50 rounded">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <button @click="rows = rows.filter((_, i) => i !== index); saveData()" 
                                                    class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

                            <!-- Footer -->
                            <tr class="bg-gray-50 text-black">
                                <td colspan="2" class="py-2 px-4 border font-medium text-center">JUMLAH HPP</td>
                                <td class="py-2 px-4 border"></td>
                                <td class="py-2 px-4 border text-right font-medium" x-text="formatNumber(getTotal())"></td>
                                <td class="py-2 px-4 border"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection