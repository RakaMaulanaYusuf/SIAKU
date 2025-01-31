@extends('main')

@section('title', 'Laba Rugi')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col" x-data="{ 
    searchTerm: '',
    selectedSection: 'PENDAPATAN',
    newRow: {
        code: '',
        name: '',
        amount: '',
        isEditing: false
    },
    pendapatanRows: [
        { code: '41', name: 'Pendapatan Jasa Angkutan', amount: '526', isEditing: false }
    ],
    hppRows: [
        { code: '51', name: 'Beban Gaji', amount: '40', isEditing: false },
        { code: '52', name: 'Beban Bensin', amount: '75', isEditing: false },
        { code: '53', name: 'Beban Makan dan Minum', amount: '50', isEditing: false },
        { code: '54', name: 'Beban Perawatan', amount: '15', isEditing: false }
    ],
    operasionalRows: [],
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
                        <h1 class="text-2xl font-bold text-black">Laporan Laba Rugi</h1>
                        <p class="text-sm text-gray-600 mt-1">31 Juni 2018</p>
                    </div>
                    <div class="flex gap-3">
                        <select x-model="selectedSection" 
                            class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Section</option>
                            <option value="PENDAPATAN">Pendapatan</option>
                            <option value="HPP">Jumlah HPP</option>
                            <option value="OPERASIONAL">Biaya Operasional</option>
                        </select>
                    </div>
                </div>
                
                <!-- Reusable Table Template -->
                <template x-if="selectedSection === '' || selectedSection === 'PENDAPATAN'">
                    <div class="overflow-x-auto border border-gray-200 rounded-lg mb-6">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-red-600 text-white text-sm">
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
                                                pendapatanRows.push({...newRow, isEditing: false});
                                                newRow = {
                                                    code: '',
                                                    name: '',
                                                    amount: '',
                                                    isEditing: false
                                                }
                                            }"
                                            class="p-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center justify-center w-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <!-- Existing Rows -->
                                <template x-for="(row, index) in pendapatanRows" :key="index">
                                    <tr class="hover:bg-gray-50">
                                        <template x-if="!row.isEditing">
                                            <tr>
                                                <td class="py-2 px-4 border" x-text="row.code"></td>
                                                <td class="py-2 px-4 border" x-text="row.name"></td>
                                                <td class="py-2 px-4 border text-right" x-text="new Intl.NumberFormat('id-ID').format(row.amount)"></td>
                                                <td class="py-2 px-4 border"></td>
                                                <td class="py-2 px-4 border text-center">
                                                    <div class="flex justify-center gap-2">
                                                        <button @click="row.isEditing = true" 
                                                            class="p-1 text-blue-600 hover:bg-blue-50 rounded flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                            </svg>
                                                        </button>
                                                        <button @click="pendapatanRows.splice(index, 1)" 
                                                            class="p-1 text-red-600 hover:bg-red-50 rounded flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="row.isEditing">
                                            <tr class="bg-blue-50">
                                                <td class="py-2 px-4 border">
                                                    <input type="text" x-model="row.code"
                                                        class="w-full px-2 py-1.5 border rounded text-sm">
                                                </td>
                                                <td class="py-2 px-4 border">
                                                    <input type="text" x-model="row.name"
                                                        class="w-full px-2 py-1.5 border rounded text-sm">
                                                </td>
                                                <td class="py-2 px-4 border">
                                                    <input type="number" x-model="row.amount"
                                                        class="w-full px-2 py-1.5 border rounded text-sm text-right">
                                                </td>
                                                <td class="py-2 px-4 border"></td>
                                                <td class="py-2 px-4 border text-center">
                                                    <div class="flex justify-center gap-2">
                                                        <button @click="row.isEditing = false" 
                                                            class="px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700">
                                                            Simpan
                                                        </button>
                                                        <button @click="row.isEditing = false" 
                                                            class="p-1 text-red-600 hover:bg-red-50 rounded flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                            Batal
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tr>
                                </template>
                                <!-- Pendapatan Footer -->
                                <tr class="bg-red-600 text-white">
                                    <td colspan="2" class="py-2 px-4 border font-medium">JUMLAH PENDAPATAN</td>
                                    <td class="py-2 px-4 border text-right font-medium" 
                                        x-text="new Intl.NumberFormat('id-ID').format(getTotalPendapatan())"></td>
                                    <td colspan="2" class="py-2 px-4 border"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>

                <!-- HPP Section -->
                <template x-if="selectedSection === '' || selectedSection === 'HPP'">
                    <div class="overflow-x-auto border border-gray-200 rounded-lg mb-6">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-red-600 text-white text-sm">
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
                                                hppRows.push({...newRow, isEditing: false});
                                                newRow = {
                                                    code: '',
                                                    name: '',
                                                    amount: '',
                                                    isEditing: false
                                                }
                                            }"
                                            class="p-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center justify-center w-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <!-- Existing Rows -->
                                <template x-for="(row, index) in hppRows" :key="index">
                                    <tr class="hover:bg-gray-50">
                                        <template x-if="!row.isEditing">
                                            <tr>
                                                <td class="py-2 px-4 border" x-text="row.code"></td>
                                                <td class="py-2 px-4 border" x-text="row.name"></td>
                                                <td class="py-2 px-4 border text-right" x-text="new Intl.NumberFormat('id-ID').format(row.amount)"></td>
                                                <td class="py-2 px-4 border"></td>
                                                <td class="py-2 px-4 border text-center">
                                                    <div class="flex justify-center gap-2">
                                                        <button @click="row.isEditing = true" 
                                                            class="p-1 text-blue-600 hover:bg-blue-50 rounded flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                            </svg>
                                                        </button>
                                                        <button @click="hppRows.splice(index, 1)" 
                                                            class="p-1 text-red-600 hover:bg-red-50 rounded flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="row.isEditing">
                                            <tr class="bg-blue-50">
                                                <td class="py-2 px-4 border">
                                                    <input type="text" x-model="row.code"
                                                        class="w-full px-2 py-1.5 border rounded text-sm">
                                                </td>
                                                <td class="py-2 px-4 border">
                                                    <input type="text" x-model="row.name"
                                                        class="w-full px-2 py-1.5 border rounded text-sm">
                                                </td>
                                                <td class="py-2 px-4 border">
                                                    <input type="number" x-model="row.amount"
                                                        class="w-full px-2 py-1.5 border rounded text-sm text-right">
                                                </td>
                                                <td class="py-2 px-4 border"></td>
                                                <td class="py-2 px-4 border text-center">
                                                    <div class="flex justify-center gap-2">
                                                        <button @click="row.isEditing = false" 
                                                            class="p-1 text-green-600 hover:bg-green-50 rounded flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Simpan
                                                        </button>
                                                        <button @click="row.isEditing = false" 
                                                            class="p-1 text-red-600 hover:bg-red-50 rounded flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                            Batal
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tr>
                                </template>
                                <!-- HPP Footer -->
                                <tr class="bg-red-600 text-white">
                                    <td colspan="2" class="py-2 px-4 border font-medium">JUMLAH HPP</td>
                                    <td class="py-2 px-4 border text-right font-medium" 
                                        x-text="new Intl.NumberFormat('id-ID').format(getTotalHPP())"></td>
                                    <td colspan="2" class="py-2 px-4 border"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>

                <!-- Operasional Section -->
                <template x-if="selectedSection === '' || selectedSection === 'OPERASIONAL'">
                    <div class="overflow-x-auto border border-gray-200 rounded-lg mb-6">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-red-600 text-white text-sm">
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
                                                operasionalRows.push({...newRow, isEditing: false});
                                                newRow = {
                                                    code: '',
                                                    name: '',
                                                    amount: '',
                                                    isEditing: false
                                                }
                                            }"
                                            class="p-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center justify-center w-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                            </svg>
                                            Tambah
                                        </button>
                                    </td>
                                </tr>
                                <!-- Existing Rows -->
                                <template x-for="(row, index) in operasionalRows" :key="index">
                                    <tr class="hover:bg-gray-50">
                                        <template x-if="!row.isEditing">
                                            <tr>
                                                <td class="py-2 px-4 border" x-text="row.code"></td>
                                                <td class="py-2 px-4 border" x-text="row.name"></td>
                                                <td class="py-2 px-4 border text-right" x-text="new Intl.NumberFormat('id-ID').format(row.amount)"></td>
                                                <td class="py-2 px-4 border"></td>
                                                <td class="py-2 px-4 border text-center">
                                                    <div class="flex justify-center gap-2">
                                                        <button @click="row.isEditing = true" 
                                                            class="p-1 text-blue-600 hover:bg-blue-50 rounded flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                            </svg>
                                                            Edit
                                                        </button>
                                                        <button @click="operasionalRows.splice(index, 1)" 
                                                            class="p-1 text-red-600 hover:bg-red-50 rounded flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                            </svg>
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="row.isEditing">
                                            <tr class="bg-blue-50">
                                                <td class="py-2 px-4 border">
                                                    <input type="text" x-model="row.code"
                                                        class="w-full px-2 py-1.5 border rounded text-sm">
                                                </td>
                                                <td class="py-2 px-4 border">
                                                    <input type="text" x-model="row.name"
                                                        class="w-full px-2 py-1.5 border rounded text-sm">
                                                </td>
                                                <td class="py-2 px-4 border">
                                                    <input type="number" x-model="row.amount"
                                                        class="w-full px-2 py-1.5 border rounded text-sm text-right">
                                                </td>
                                                <td class="py-2 px-4 border"></td>
                                                <td class="py-2 px-4 border text-center">
                                                    <div class="flex justify-center gap-2">
                                                        <button @click="row.isEditing = false" 
                                                            class="p-1 text-green-600 hover:bg-green-50 rounded flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Simpan
                                                        </button>
                                                        <button @click="row.isEditing = false" 
                                                            class="p-1 text-red-600 hover:bg-red-50 rounded flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                            Batal
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tr>
                                </template>
                                <!-- Operasional Footer -->
                                <tr class="bg-red-600 text-white">
                                    <td colspan="2" class="py-2 px-4 border font-medium">JUMLAH BIAYA OPERASIONAL</td>
                                    <td class="py-2 px-4 border text-right font-medium" 
                                        x-text="new Intl.NumberFormat('id-ID').format(getTotalOperasional())"></td>
                                    <td colspan="2" class="py-2 px-4 border"></td>
                                </tr>
                                <!-- Laba Bersih Footer -->
                                <tr class="bg-red-600 text-white">
                                    <td colspan="2" class="py-2 px-4 border font-medium">LABA BERSIH</td>
                                    <td class="py-2 px-4 border text-right font-medium" 
                                        x-text="new Intl.NumberFormat('id-ID').format(getLabaBersih())"></td>
                                    <td colspan="2" class="py-2 px-4 border"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection