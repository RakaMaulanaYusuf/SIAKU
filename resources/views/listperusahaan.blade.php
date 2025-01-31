@extends('main')

@section('title', 'List Perusahaan')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col" x-data="{
    openDrawer: false,
    searchTerm: '',
    searchMonth: '',
    searchYear: '',
    companies: {{ Js::from($companies) }},
    activeCompany: {{ Js::from($activeCompany) }}
}">
    <div class="flex overflow-hidden">
        <x-side-bar-menu></x-side-bar-menu>
        
        <div id="main-content" class="relative text-black ml-72 font-poppins w-full h-full overflow-y-auto">
            <!-- Current Company Indicator -->
            @if(auth()->user()->activeCompany)
                <div class="sticky top-0 bg-blue-600 text-white py-2 px-6 flex justify-between items-center z-50">
                    <div class="flex items-center">
                        <span class="font-medium">Perusahaan Aktif:</span>
                        <span class="ml-2">{{ auth()->user()->activeCompany->name }}</span>
                    </div>
                    <a href="{{ route('listP') }}" class="text-sm bg-blue-700 px-3 py-1 rounded hover:bg-blue-800 transition-colors">
                        Ganti Perusahaan
                    </a>
                </div>
            @endif
            <!-- Header Box -->
            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-black">Daftar Perusahaan</h1>
                        <p class="text-gray-600 mt-1">Pilih perusahaan yang ingin dikerjakan</p>
                    </div>
                    <button @click="openDrawer = true" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Perusahaan
                    </button>
                </div>

                <!-- Search Bar -->
                <div class="mt-6 flex gap-4">
                    <div class="flex-1">
                        <input type="text" 
                            x-model="searchTerm"
                            placeholder="Cari nama perusahaan..." 
                            class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex gap-2">
                        <select x-model="searchMonth" 
                            class="px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Bulan</option>
                            <option value="Januari">Januari</option>
                            <option value="Februari">Februari</option>
                            <option value="Maret">Maret</option>
                            <option value="April">April</option>
                            <option value="Mei">Mei</option>
                            <option value="Juni">Juni</option>
                            <option value="Juli">Juli</option>
                            <option value="Agustus">Agustus</option>
                            <option value="September">September</option>
                            <option value="Oktober">Oktober</option>
                            <option value="November">November</option>
                            <option value="Desember">Desember</option>
                        </select>
                        <input type="number" 
                            x-model="searchYear"
                            placeholder="Tahun"
                            min="2000" max="2099"
                            class="w-32 px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Company Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                <template x-for="company in companies.filter(c => {
                    let matchesName = c.name.toLowerCase().includes(searchTerm.toLowerCase());
                    let matchesMonth = searchMonth === '' || c.period_month === searchMonth;
                    let matchesYear = searchYear === '' || parseInt(c.period_year) === parseInt(searchYear);
                    return matchesName && matchesMonth && matchesYear;
                })" :key="company.id">                
                    <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-all cursor-pointer">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <span class="text-xl font-bold text-blue-600" x-text="company.initial"></span>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-semibold" x-text="company.name"></h3>
                                <p class="text-sm text-gray-500" x-text="company.type"></p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Status</span>
                                <span class="text-green-500 font-medium" x-text="company.status"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Periode</span>
                                <span x-text="company.period_month + ' ' + company.period_year"></span>
                            </div>
                        </div>
                        <form x-bind:action="'{{ url('companies') }}/' + company.id + '/set-active'" method="POST">
                            @csrf
                            <button type="submit" class="w-full mt-4 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                Pilih Perusahaan
                            </button>
                        </form>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Add Company Drawer -->
    <div class="fixed inset-0 overflow-hidden z-50" x-show="openDrawer" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" 
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100" 
        x-transition:leave-end="opacity-0">
        
        <!-- Overlay -->
        <div class="absolute inset-0 bg-gray-500 bg-opacity-75" @click="openDrawer = false"></div>

        <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
            <div class="relative w-96"
                x-transition:enter="transform transition ease-in-out duration-500"
                x-transition:enter-start="translate-x-full" 
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-500"
                x-transition:leave-start="translate-x-0" 
                x-transition:leave-end="translate-x-full">
                
                <div class="h-full bg-white shadow-xl overflow-y-auto">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold">Tambah Perusahaan</h2>
                            <button @click="openDrawer = false" class="text-gray-500 hover:text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form @submit.prevent="
                            fetch('{{ route('companies.store') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    name: $event.target.name.value,
                                    type: $event.target.type.value,
                                    period_month: $event.target.month.value,
                                    period_year: $event.target.year.value
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if(data.success) {
                                    companies.push(data.company);
                                    openDrawer = false;
                                    $event.target.reset();
                                }
                            })">
                            <div>
                                <label class="block text-sm font-medium mb-1">Nama Perusahaan</label>
                                <input type="text" name="name" required 
                                    placeholder="Masukkan nama perusahaan"
                                    class="w-full border rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium mb-1">Jenis Usaha</label>
                                <input type="text" name="type" required 
                                    placeholder="Masukkan jenis usaha"
                                    class="w-full border rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Periode</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <select name="month" required
                                            class="w-full border rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Pilih Bulan</option>
                                            <option value="Januari">Januari</option>
                                            <option value="Februari">Februari</option>
                                            <option value="Maret">Maret</option>
                                            <option value="April">April</option>
                                            <option value="Mei">Mei</option>
                                            <option value="Juni">Juni</option>
                                            <option value="Juli">Juli</option>
                                            <option value="Agustus">Agustus</option>
                                            <option value="September">September</option>
                                            <option value="Oktober">Oktober</option>
                                            <option value="November">November</option>
                                            <option value="Desember">Desember</option>
                                        </select>
                                    </div>
                                    <div>
                                        <input type="number" name="year" required 
                                            placeholder="Tahun"
                                            min="2000" max="2099"
                                            class="w-full border rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection