@extends('main')

@section('title', 'Neraca')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col" x-data="{ 
    searchTerm: '',
    balanceSheet: {
        assets: [
            { type: 'Aset Lancar', accounts: [
                { code: '11', name: 'Kas', amount: '10000000' },
                { code: '12', name: 'Piutang Usaha', amount: '5000000' }
            ]},
            { type: 'Aset Tetap', accounts: [
                { code: '15', name: 'Peralatan', amount: '15000000' }
            ]}
        ],
        liabilities: [
            { type: 'Kewajiban Lancar', accounts: [
                { code: '21', name: 'Utang Usaha', amount: '8000000' }
            ]}
        ],
        equity: [
            { type: 'Modal', accounts: [
                { code: '31', name: 'Modal Pemilik', amount: '22000000' }
            ]}
        ]
    }
}">
    <!-- [Previous header code remains the same] -->

    <!-- Table Section -->
    <div class="overflow-x-auto border border-gray-200 rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-red-600 text-white text-sm">
                    <th colspan="2" class="py-3 px-4 text-center border-b border-r">AKTIVA</th>
                    <th colspan="2" class="py-3 px-4 text-center border-b">PASIVA</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                <!-- Assets and Liabilities -->
                <tr>
                    <td colspan="2" class="p-0 border-r">
                        <table class="w-full">
                            <template x-for="(section, sIndex) in balanceSheet.assets" :key="sIndex">
                                <template>
                                    <tr class="bg-gray-50 font-medium">
                                        <td colspan="2" class="py-2 px-4" x-text="section.type"></td>
                                    </tr>
                                    <template x-for="(account, aIndex) in section.accounts" :key="aIndex">
                                        <tr>
                                            <td class="py-2 px-4" x-text="account.name"></td>
                                            <td class="py-2 px-4 text-right w-48" 
                                                x-text="new Intl.NumberFormat('id-ID').format(account.amount)"></td>
                                        </tr>
                                    </template>
                                </template>
                            </template>
                        </table>
                    </td>
                    <td colspan="2" class="p-0">
                        <table class="w-full">
                            <!-- Liabilities -->
                            <template x-for="(section, sIndex) in balanceSheet.liabilities" :key="sIndex">
                                <template>
                                    <tr class="bg-gray-50 font-medium">
                                        <td colspan="2" class="py-2 px-4" x-text="section.type"></td>
                                    </tr>
                                    <template x-for="(account, aIndex) in section.accounts" :key="aIndex">
                                        <tr>
                                            <td class="py-2 px-4" x-text="account.name"></td>
                                            <td class="py-2 px-4 text-right w-48" 
                                                x-text="new Intl.NumberFormat('id-ID').format(account.amount)"></td>
                                        </tr>
                                    </template>
                                </template>
                            </template>
                            <!-- Equity -->
                            <template x-for="(section, sIndex) in balanceSheet.equity" :key="sIndex">
                                <template>
                                    <tr class="bg-gray-50 font-medium">
                                        <td colspan="2" class="py-2 px-4" x-text="section.type"></td>
                                    </tr>
                                    <template x-for="(account, aIndex) in section.accounts" :key="aIndex">
                                        <tr>
                                            <td class="py-2 px-4" x-text="account.name"></td>
                                            <td class="py-2 px-4 text-right w-48" 
                                                x-text="new Intl.NumberFormat('id-ID').format(account.amount)"></td>
                                        </tr>
                                    </template>
                                </template>
                            </template>
                        </table>
                    </td>
                </tr>
                <!-- Totals -->
                <tr class="bg-gray-100 font-bold">
                    <td colspan="2" class="py-3 px-4 text-right border-r">
                        Total Aktiva: 
                        <span x-text="new Intl.NumberFormat('id-ID').format(
                            balanceSheet.assets.reduce((total, section) => 
                                total + section.accounts.reduce((sum, acc) => sum + Number(acc.amount), 0), 0)
                        )"></span>
                    </td>
                    <td colspan="2" class="py-3 px-4 text-right">
                        Total Pasiva: 
                        <span x-text="new Intl.NumberFormat('id-ID').format(
                            [...balanceSheet.liabilities, ...balanceSheet.equity].reduce((total, section) => 
                                total + section.accounts.reduce((sum, acc) => sum + Number(acc.amount), 0), 0)
                        )"></span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection