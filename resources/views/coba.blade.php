<template x-if="account.isEditing">
    <td colspan="8">
        <div class="flex gap-2">
            <input type="number" x-model="account.code" 
                class="px-2 py-1.5 border rounded text-sm"
                style="width: 60px;">
            <input type="text" x-model="account.name" 
                class="flex-1 px-2 py-1.5 border rounded text-sm">
            <input type="number" x-model="account.table" 
                class="px-2 py-1.5 border rounded text-sm"
                style="width: 60px;">
            <select x-model="account.balance_type" 
                class="w-28 px-2 py-1.5 border rounded text-sm">
                <option value="DEBET">DEBET</option>
                <option value="KREDIT">KREDIT</option>
            </select>
            <select x-model="account.report_type" 
                class="w-28 px-2 py-1.5 border rounded text-sm">
                <option value="NERACA">NERACA</option>
                <option value="LABA_RUGI">LABA RUGI</option>
            </select>
            <input type="number" x-model="account.debit" 
                class="w-36 px-2 py-1.5 border rounded text-sm text-right">
            <input type="number" x-model="account.credit" 
                class="w-36 px-2 py-1.5 border rounded text-sm text-right">
            <button @click="account.isEditing = false" 
                class="px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700">
                Simpan
            </button>
        </div>
    </td>
</template>