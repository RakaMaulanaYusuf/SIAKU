@extends('main')

@section('title', 'List Perusahaan')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col" x-data="{
    openDrawer: false,
    openPeriodModal: false,
    searchTerm: '',
    searchMonth: '',
    searchYear: '',
    companies: {{ Js::from($companies) }},
    activeCompany: {{ Js::from($activeCompany) }},
    selectedCompanyId: null,
    selectedPeriodMonth: {},
    selectedPeriodYear: {},
    
    init() {
        // Initialize period selections
        this.companies.forEach(company => {
            this.selectedPeriodMonth[company.id] = '';
            this.selectedPeriodYear[company.id] = '';
        });
        console.log('Initialized with companies:', this.companies);
    },
    
    filteredCompanies() {
        return this.companies.filter(c => {
            let matchesName = c.name.toLowerCase().includes(this.searchTerm.toLowerCase());
            let matchesMonth = this.searchMonth === '' || 
                c.periods.some(p => p.period_month === this.searchMonth);
            let matchesYear = this.searchYear === '' || 
                c.periods.some(p => p.period_year == this.searchYear);
            return matchesName && matchesMonth && matchesYear;
        });
    },

    selectCompany(company) {
        console.log('Selecting company:', company);
        this.selectedCompanyId = company.id;
        this.selectedPeriodMonth[company.id] = '';
        this.selectedPeriodYear[company.id] = '';
    },

    getCompanyYears(company) {
        if (!company.periods || company.periods.length === 0) {
            console.log('No periods found for company:', company.name);
            return [];
        }
        const years = [...new Set(company.periods.map(p => p.period_year))];
        const sortedYears = years.sort((a, b) => b - a);
        console.log('Available years for company:', company.name, sortedYears);
        return sortedYears;
    },

    getAvailableMonths(company, year) {
        if (!year || !company.periods) {
            console.log('No year selected or no periods');
            return [];
        }
        
        const monthOrder = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        const yearPeriods = company.periods.filter(p => parseInt(p.period_year) === parseInt(year));
        console.log('Periods for year', year, ':', yearPeriods);
        
        const availableMonths = yearPeriods.map(p => p.period_month);
        console.log('Available months:', availableMonths);
        
        return monthOrder.filter(month => availableMonths.includes(month));
    },

    getPeriodId(company, month, year) {
        console.log('Looking for period:', { month, year });
        console.log('Company periods:', company.periods);
        
        const period = company.periods.find(p => 
            p.period_month === month && 
            parseInt(p.period_year) === parseInt(year)
        );
        
        console.log('Found period:', period);
        return period ? period.id : null;
    },

    submitCompanySelection(company) {
        const month = this.selectedPeriodMonth[company.id];
        const year = this.selectedPeriodYear[company.id];
        
        console.log('Submitting selection:', { company, month, year });
        
        if (!month || !year) {
            alert('Pilih bulan dan tahun terlebih dahulu');
            return;
        }

        const periodId = this.getPeriodId(company, month, year);
        if (!periodId) {
            alert('Periode yang dipilih tidak tersedia');
            return;
        }

        fetch(`{{ url('companies') }}/${company.id}/set-active`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                period_id: periodId
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal mengubah perusahaan dan periode');
            }
            return response.json();
        })
        .then(result => {
            if (result.success) {
                window.location.reload();
            } else {
                alert(result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message);
        });
    },
    
    addCompany(event) {
        const formData = new FormData(event.target);
        const data = {
            name: formData.get('name'),
            type: formData.get('type'),
            address: formData.get('address'),
            phone: formData.get('phone'),
            email: formData.get('email'),
            period_month: formData.get('period_month'),
            period_year: formData.get('period_year')
        };

        fetch('{{ route('companies.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                this.companies.push(result.company);
                this.openDrawer = false;
                event.target.reset();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal menambahkan perusahaan: ' + error.message);
        });
    },

    addPeriod(event) {
        const formData = new FormData(event.target);
        const data = {
            company_id: parseInt(formData.get('company_id')),
            period_month: formData.get('period_month'),
            period_year: formData.get('period_year')
        };

        fetch('{{ route('periods.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const company = this.companies.find(c => c.id === data.company_id);
                if (company) {
                    if (!company.periods) company.periods = [];
                    company.periods.push(result.period);
                }
                this.openPeriodModal = false;
                event.target.reset();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal menambahkan periode: ' + error.message);
        });
    }
}">
    <div class="flex overflow-hidden">
        <x-side-bar-menu></x-side-bar-menu>
        <div id="main-content" class="relative text-black font-poppins w-full h-full overflow-y-auto">
            <x-nav-bar></x-nav-bar>
            <!-- Header -->
            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">Daftar Perusahaan & Periode</h1>
                        <p class="text-gray-600 mt-1">Pilih perusahaan dan periode kerja</p>
                    </div>
                    <div class="flex gap-2">
                        <button @click="openPeriodModal = true" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Tambah Periode
                        </button>
                        <button @click="openDrawer = true" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Perusahaan
                        </button>
                    </div>
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
                <template x-for="company in filteredCompanies()" :key="company.id">
                    <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-all"
                         :class="{'ring-2 ring-blue-500': selectedCompanyId === company.id}"
                         @click="selectCompany(company)">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <span class="text-xl font-bold text-blue-600" x-text="company.name.charAt(0)"></span>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-semibold" x-text="company.name"></h3>
                                <p class="text-sm text-gray-500" x-text="company.type"></p>
                            </div>
                        </div>
                        
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Status</span>
                                <span class="text-green-500 font-medium" x-text="company.status"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Email</span>
                                <span class="text-gray-500 font-medium" x-text="company.email"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">No Telepon</span>
                                <span class="text-green-500 font-medium" x-text="company.phone"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Alamat</span>
                                <span class="text-green-500 font-medium" x-text="company.address"></span>
                            </div>
                        </div>

                        <!-- Period Selection -->
                        <div class="mt-4" @click.stop>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Tahun:</label>
                                    <select 
                                        x-model="selectedPeriodYear[company.id]"
                                        @change="selectedPeriodMonth[company.id] = ''"
                                        class="mt-1 w-full border rounded-md p-2">
                                        <option value="">Pilih Tahun</option>
                                        <template x-for="year in getCompanyYears(company)" :key="year">
                                            <option :value="year" x-text="year"></option>
                                        </template>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Bulan:</label>
                                    <select 
                                        x-model="selectedPeriodMonth[company.id]"
                                        :disabled="!selectedPeriodYear[company.id]"
                                        @change="console.log('Selected month:', selectedPeriodMonth[company.id])"
                                        class="mt-1 w-full border rounded-md p-2">
                                        <option value="">Pilih Bulan</option>
                                        <template x-for="month in getAvailableMonths(company, selectedPeriodYear[company.id])" :key="month">
                                            <option :value="month" x-text="month"></option>
                                        </template>
                                    </select>
                                    <!-- Debug info -->
                                    <div x-show="false">
                                        <p x-text="'Selected Year: ' + selectedPeriodYear[company.id]"></p>
                                        <p x-text="'Available Months: ' + JSON.stringify(getAvailableMonths(company, selectedPeriodYear[company.id]))"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button @click.stop="submitCompanySelection(company)" 
                            :disabled="!selectedPeriodMonth[company.id] || !selectedPeriodYear[company.id]"
                            class="w-full mt-4 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400">
                            Pilih Perusahaan & Periode
                        </button>
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
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form @submit.prevent="addCompany" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Nama Perusahaan</label>
                                <input type="text" name="name" required 
                                    placeholder="Masukkan nama perusahaan"
                                    class="w-full border rounded-md p-2">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium mb-1">Jenis Usaha</label>
                                <input type="text" name="type" required 
                                    placeholder="Masukkan jenis usaha"
                                    class="w-full border rounded-md p-2">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Alamat</label>
                                <input type="text" name="address" required 
                                    placeholder="Masukkan alamat perusahaan"
                                    class="w-full border rounded-md p-2">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">No.Telephon</label>
                                <input type="text" name="phone" required 
                                    placeholder="Masukkan no.tlpn perusahaan"
                                    class="w-full border rounded-md p-2">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Email</label>
                                <input type="text" name="email" required 
                                    placeholder="Masukkan email perusahaan"
                                    class="w-full border rounded-md p-2">
                            </div>

                            <div class="space-y-3">
                                <label class="block text-sm font-medium mb-1">Periode Awal</label>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Tahun</label>
                                    <input type="number" name="period_year" required 
                                        placeholder="Tahun"
                                        min="2000" max="2099"
                                        class="w-full border rounded-md p-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Bulan</label>
                                    <select name="period_month" required
                                        class="w-full border rounded-md p-2">
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
                            </div>

                            <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md">
                                Simpan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Period Modal -->
    <div class="fixed inset-0 z-50" x-show="openPeriodModal" x-cloak>
        <div class="absolute inset-0 bg-gray-500 bg-opacity-75" @click="openPeriodModal = false"></div>
        
        <div class="relative z-10 flex min-h-screen items-center justify-center p-4">
            <div class="bg-white rounded-lg w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Tambah Periode Baru</h3>
                    <button @click="openPeriodModal = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
 
                <form @submit.prevent="addPeriod" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Perusahaan</label>
                        <select name="company_id" required class="w-full border rounded-md p-2">
                            <option value="">Pilih Perusahaan</option>
                            <template x-for="company in companies" :key="company.id">
                                <option :value="company.id" x-text="company.name"></option>
                            </template>
                        </select>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium mb-1">Tahun</label>
                            <input type="number" name="period_year" required
                                min="2000" max="2099"
                                class="w-full border rounded-md p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Bulan</label>
                            <select name="period_month" required class="w-full border rounded-md p-2">
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
                    </div>
 
                    <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">
                        Simpan Periode
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection