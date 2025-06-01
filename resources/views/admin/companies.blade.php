@extends('main')

@section('title', 'Daftar Perusahaan')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex overflow-hidden">
        <x-side-bar-admin></x-side-bar-admin>
        
        <div id="main-content" class="relative text-black font-poppins w-full h-full overflow-y-auto">
            <!-- Header Box -->
            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-black">Daftar Perusahaan</h1>
                        <p class="text-gray-600 mt-1">Kelola semua perusahaan yang terdaftar dalam sistem</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Total Perusahaan</p>
                            <p class="font-semibold">{{ $companies->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Companies Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @forelse($companies as $company)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <!-- Company Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-100 rounded-lg">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $company->name }}</h3>
                                    <p class="text-sm text-gray-500">ID: {{ $company->id }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Company Details -->
                        <div class="space-y-3 mb-4">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Tipe:</span>
                                <span class="text-sm font-medium">{{ $company->type ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Status:</span>
                                <span class="px-2 py-1 text-xs rounded-full {{ $company->status_badge }}">
                                    {{ ucfirst($company->status ?? 'active') }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Total Periode:</span>
                                <span class="text-sm font-medium">{{ $company->periods->count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Assigned Viewers:</span>
                                <span class="text-sm font-medium">{{ $company->assignedViewers->count() }}</span>
                            </div>
                        </div>

                        <!-- Periods List -->
                        @if($company->periods->count() > 0)
                        <div class="border-t pt-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Periode Tersedia:</h4>
                            <div class="space-y-1 max-h-32 overflow-y-auto">
                                @foreach($company->periods->sortByDesc('period_year')->sortBy(function($period) {
                                    $months = ['Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4, 'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8, 'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12];
                                    return $months[$period->period_month] ?? 13;
                                }) as $period)
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-600">{{ $period->period_name }}</span>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full">
                                        {{ $period->period_month }} {{ $period->period_year }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Assigned Viewers -->
                        @if($company->assignedViewers->count() > 0)
                        <div class="border-t pt-4 mt-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Viewers yang Di-assign:</h4>
                            <div class="space-y-2">
                                @foreach($company->assignedViewers as $viewer)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-yellow-600">
                                                {{ substr($viewer->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <span class="ml-2 text-xs text-gray-700">{{ $viewer->name }}</span>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $viewer->email }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Actions -->
                        <div class="border-t pt-4 mt-4">
                            <div class="flex justify-between items-center">
                                <a href="{{ route('admin.assign-company') }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Assign Viewer
                                </a>
                                <div class="flex space-x-2">
                                    <button class="text-gray-600 hover:text-gray-800" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                        <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Perusahaan</h3>
                        <p class="text-gray-500">Belum ada perusahaan yang terdaftar dalam sistem.</p>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 p-6">
                <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $companies->count() }}</div>
                    <div class="text-sm text-gray-600">Total Perusahaan</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $companies->sum(function($company) { return $company->periods->count(); }) }}</div>
                    <div class="text-sm text-gray-600">Total Periode</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $companies->sum(function($company) { return $company->assignedViewers->count(); }) }}</div>
                    <div class="text-sm text-gray-600">Viewers Assigned</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                    <div class="text-2xl font-bold text-gray-600">{{ $companies->where('created_at', '>=', now()->startOfMonth())->count() }}</div>
                    <div class="text-sm text-gray-600">Baru Bulan Ini</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection