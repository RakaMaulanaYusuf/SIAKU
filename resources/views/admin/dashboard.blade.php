@extends('main')

@section('title', 'Admin Dashboard')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex overflow-hidden">
        <x-side-bar-admin></x-side-bar-admin>
        
        <div id="main-content" class="relative text-black font-poppins w-full h-full overflow-y-auto">
            <!-- Header Box -->
            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-black">Dashboard Admin</h1>
                        <p class="text-gray-600 mt-1">Overview sistem dan manajemen pengguna</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Tanggal</p>
                            <p class="font-semibold">{{ now()->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                <!-- Total Users -->
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Pengguna</p>
                            <h3 class="text-2xl font-bold mt-1">{{ $totalUsers }}</h3>
                            <p class="text-blue-500 text-sm mt-1">Semua role</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Staff -->
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Akun Staff</p>
                            <h3 class="text-2xl font-bold mt-1">{{ $totalStaff }}</h3>
                            <p class="text-green-500 text-sm mt-1">Staff aktif</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Viewers -->
                {{-- <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Akun Viewer</p>
                            <h3 class="text-2xl font-bold mt-1">{{ $totalViewers }}</h3>
                            <p class="text-yellow-500 text-sm mt-1">
                                {{ $unassignedViewers }} belum di-assign
                            </p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div> --}}

                <!-- Total Companies -->
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Perusahaan</p>
                            <h3 class="text-2xl font-bold mt-1">{{ $totalCompanies }}</h3>
                            <p class="text-purple-500 text-sm mt-1">Terdaftar</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                <!-- Recent Users -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold">Pengguna Terbaru</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse($recentUsers as $user)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="p-2 
                                        @if($user->role === 'admin') bg-red-100 @endif
                                        @if($user->role === 'staff') bg-green-100 @endif
                                        @if($user->role === 'viewer') bg-yellow-100 @endif
                                        rounded-lg">
                                        <svg class="w-6 h-6 
                                            @if($user->role === 'admin') text-red-600 @endif
                                            @if($user->role === 'staff') text-green-600 @endif
                                            @if($user->role === 'viewer') text-yellow-600 @endif
                                            " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($user->role === 'admin') bg-red-100 text-red-800 @endif
                                    @if($user->role === 'staff') bg-green-100 text-green-800 @endif
                                    @if($user->role === 'viewer') bg-yellow-100 text-yellow-800 @endif
                                    ">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center py-4">Belum ada pengguna terbaru</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold">Aksi Cepat</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <a href="{{ route('admin.manage-accounts') }}" 
                               class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors group">
                                <div class="p-3 bg-blue-500 rounded-full group-hover:bg-blue-600 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                                <span class="mt-2 text-sm font-medium text-gray-700 group-hover:text-blue-600">Kelola Akun</span>
                            </a>

                            {{-- <a href="{{ route('admin.assign-company') }}" 
                               class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors group">
                                <div class="p-3 bg-green-500 rounded-full group-hover:bg-green-600 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <span class="mt-2 text-sm font-medium text-gray-700 group-hover:text-green-600">Assign Perusahaan</span>
                            </a> --}}

                            <a href="{{ route('admin.companies') }}" 
                               class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors group">
                                <div class="p-3 bg-purple-500 rounded-full group-hover:bg-purple-600 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <span class="mt-2 text-sm font-medium text-gray-700 group-hover:text-purple-600">Lihat Perusahaan</span>
                            </a>

                            {{-- <div class="flex flex-col items-center p-4 bg-gray-50 rounded-lg">
                                <div class="p-3 bg-gray-400 rounded-full">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <span class="mt-2 text-sm font-medium text-gray-500">Lainnya</span>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection