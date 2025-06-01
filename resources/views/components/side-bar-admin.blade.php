<!-- CSS untuk SweetAlert agar tombol terlihat -->
<style>
.swal2-confirm {
    color: white !important;
    background-color: #3085d6 !important;
}

.swal2-cancel {
    color: white !important;
    background-color: #d33 !important;
}

.swal2-styled {
    color: white !important;
}
</style>

<div x-data="{ 
    isOpen: true,
    // Fungsi SweetAlert untuk konfirmasi logout
    confirmLogout() {
        Swal.fire({
            title: 'Konfirmasi Logout',
            text: 'Apakah Anda yakin ingin keluar dari sistem?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Logout!',
            cancelButtonText: 'Batal',
            buttonsStyling: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading alert
                Swal.fire({
                    title: 'Logging out...',
                    text: 'Mohon tunggu sebentar',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit logout form
                document.getElementById('logout-form').submit();
            }
        });
    }
}">
    <!-- Sidebar -->
    <div class="fixed left-0 top-0 h-full transform transition-all duration-300 ease-in-out z-40"
         :class="isOpen ? 'w-72' : 'w-16'">
        <div class="relative h-full bg-white shadow-lg rounded-r-3xl">
            <!-- Toggle Button -->
            <button 
                @click="isOpen = !isOpen"
                class="absolute -right-3 top-16 p-2 bg-white rounded-full shadow-lg text-gray-600 hover:text-blue-600 transform transition-all duration-300 hover:scale-105 z-50">
                <svg class="w-6 h-6 transform transition-transform duration-300" 
                     :class="isOpen ? 'rotate-0' : 'rotate-180'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <!-- Logo -->
            <div class="flex justify-center items-center py-8">
                <img src="{{ asset('images/Logo.png') }}" alt="SIAKU" 
                     class="transition-all duration-300"
                     :class="isOpen ? 'w-24' : 'w-10'">
            </div>
        
            <!-- Menu Items -->
            <nav class="mt-2" :class="isOpen ? 'px-3' : 'px-2'">
                <template x-for="(item, index) in [
                    {name: 'Dashboard Admin', route: '/admin/dashboard', icon: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'},
                    {name: 'Kelola Akun', route: '/admin/manage-accounts', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z'},
                    {name: 'Daftar Perusahaan', route: '/admin/companies', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'},
                    {name: 'Assign Perusahaan', route: '/admin/assign-company', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'}
                ]">
                    <a :href="item.route" 
                       class="flex items-center text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300 ease-in-out rounded-xl group relative mb-1"
                       :class="isOpen ? 'px-4 py-3' : 'px-3 py-3 justify-center'">
                        <div class="p-2 bg-blue-600 rounded-lg group-hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"></path>
                            </svg>
                        </div>
                        <span class="ml-3 font-medium text-sm whitespace-nowrap transition-all duration-300 overflow-hidden"
                              :class="isOpen ? 'opacity-100 w-auto' : 'opacity-0 w-0 hidden'">
                            <span x-text="item.name"></span>
                        </span>
                        <div x-show="!isOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-x-1"
                             x-transition:enter-end="opacity-100 translate-x-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-x-0"
                             x-transition:leave-end="opacity-0 translate-x-1"
                             class="absolute left-14 ml-2 bg-gray-900 text-white px-2 py-1 rounded text-sm whitespace-nowrap opacity-0 group-hover:opacity-100">
                            <span x-text="item.name"></span>
                        </div>
                    </a>
                </template>
            </nav>
        
            <!-- User Profile -->
            <div class="absolute bottom-0 w-full border-t bg-gray-50">
                <div class="flex items-center p-4" :class="isOpen ? 'justify-between' : 'justify-center'">
                    <a href="{{ route('profile.show') }}" class="flex items-center min-w-0 group hover:bg-blue-50 rounded-lg p-2 transition-all duration-200 cursor-pointer">
                        <div class="relative flex-shrink-0">
                            @if(auth()->user()->profile_photo)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" 
                                     alt="Profile" 
                                     class="w-10 h-10 rounded-full border-2 border-blue-200 transform transition-transform group-hover:scale-105 object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full border-2 border-blue-200 bg-gradient-to-br from-red-400 to-pink-500 flex items-center justify-center transform transition-transform group-hover:scale-105">
                                    <span class="text-sm font-bold text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                        </div>
                        <div class="ml-3 transition-all duration-300 overflow-hidden"
                             :class="isOpen ? 'opacity-100 w-32' : 'opacity-0 w-0 hidden'">
                            <p class="text-sm font-medium text-gray-700 truncate group-hover:text-blue-600">{{ auth()->user()->name }}</p>
                            <p class="text-xs font-medium text-red-500">{{ ucfirst(auth()->user()->role) }}</p>
                        </div>
                    </a>
                    
                    <!-- Hidden Form for Logout -->
                    <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
                        @csrf
                    </form>
                    
                    <!-- Logout Button with SweetAlert -->
                    <button 
                        @click="confirmLogout()"
                        x-show="isOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="flex items-center justify-center w-10 h-10 bg-red-50 hover:bg-red-100 text-red-600 rounded-full transition-all duration-300 group hover:rotate-12">
                        <svg class="w-5 h-5 transform transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-6 0v-1m0-8v-1a3 3 0 016 0v1"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="transition-all duration-300 ease-in-out min-h-screen"
         :class="isOpen ? 'ml-72' : 'ml-16'">
        {{ $slot ?? '' }}
    </div>
</div>

<!-- Pastikan SweetAlert2 library dimuat -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>