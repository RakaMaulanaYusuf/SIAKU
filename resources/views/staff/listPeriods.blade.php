@extends('main')

@section('title', 'Pilih Periode')

@section('page')
<div class="bg-gray-50 min-h-screen" x-data="{
    searchYear: '',
    searchMonth: '',
    periods: {{ Js::from($periods) }},
    months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
    
    filteredPeriods() {
        return this.periods.filter(p => 
            (!this.searchYear || p.period_year.toString().includes(this.searchYear)) &&
            (!this.searchMonth || p.period_month.toLowerCase().includes(this.searchMonth.toLowerCase()))
        );
    }
}">
    <div class="flex overflow-hidden">
        <x-side-bar-viewer></x-side-bar-viewer>
        
        <div id="main-content" class="relative text-black font-poppins w-full h-full overflow-y-auto">
            <x-nav-bar-viewer></x-nav-bar-viewer>

            <!-- Header & Search Section -->
            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Pilih Periode</h1>
                        <p class="text-gray-600 mt-1">{{ $company->name }}</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                        <select 
                            x-model="searchMonth"
                            class="w-full sm:w-48 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Bulan</option>
                            <template x-for="month in months" :key="month">
                                <option :value="month" x-text="month"></option>
                            </template>
                        </select>
                        <input type="number" 
                            x-model="searchYear"
                            placeholder="Cari tahun..." 
                            class="w-full sm:w-40 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Period Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                <template x-for="period in filteredPeriods()" :key="period.id">
                    <form action="{{ route('setPeriod') }}" method="POST">
                        @csrf
                        <input type="hidden" name="period_id" :value="period.id">
                        <button type="submit" 
                            class="w-full bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200
                                   border border-gray-200 hover:border-blue-500 relative overflow-hidden group">
                            <!-- Active Period Indicator -->
                            <div x-show="period.id === {{ auth()->user()->company_period_id ?? 'null' }}"
                                 class="absolute top-0 right-0 w-20 h-20">
                                <div class="absolute transform rotate-45 bg-green-500 text-white text-xs font-bold py-1 right-[-35px] top-[32px] w-[170px] text-center">
                                    Aktif
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xl font-semibold text-gray-800" 
                                    x-text="period.period_month + ' ' + period.period_year">
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <span x-text="period.id === {{ auth()->user()->company_period_id ?? 'null' }} ? 'Periode Aktif' : 'Klik untuk mengaktifkan'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" 
                                     class="h-5 w-5 text-gray-400 group-hover:text-blue-500 transform group-hover:translate-x-1 transition-all duration-200" 
                                     viewBox="0 0 20 20" 
                                     fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                    </form>
                </template>

                <!-- Empty State -->
                <div x-show="filteredPeriods().length === 0" 
                     class="col-span-full text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada periode</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Tidak ada periode yang sesuai dengan pencarian Anda
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 2000
        });
    @endif

    @if(session('warning'))
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: "{{ session('warning') }}",
            showConfirmButton: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{{ session('error') }}",
            showConfirmButton: true
        });
    @endif
});
</script>
@endpush

@endsection