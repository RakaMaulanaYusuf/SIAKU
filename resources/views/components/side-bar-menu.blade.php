<div>
    <div class="fixed left-0 top-0 w-72 h-full bg-white shadow-lg rounded-r-3xl">
        <!-- Logo -->
        <div class="flex justify-center items-center py-8">
            <img src="{{ asset('images/Logo.png') }}" alt="SIAKU" class="w-24 h-auto">
        </div>
    
        <!-- Menu Items -->
        <nav class="space-y-1 mt-2 px-3">
            <!-- Dashboard -->
            <a href="/dashboard" class="flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300 ease-in-out rounded-xl">
                <div class="p-2 bg-blue-600 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </div>
                <span class="font-medium text-sm">Dashboard</span>
            </a>

            <!-- List Perusahaan -->
            <a href="/listP" class="flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300 ease-in-out rounded-xl">
                <div class="p-2 bg-blue-600 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <span class="font-medium text-sm">List Perusahaan</span>
            </a>
    
            <!-- Kode Akun -->
            <a href="/kodeakun" class="flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300 ease-in-out rounded-xl">
                <div class="p-2 bg-blue-600 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="font-medium text-sm">Kode Akun</span>
            </a>

            <!-- Kode Bantu -->
            <a href="/kodebantu" class="flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300 ease-in-out rounded-xl">
                <div class="p-2 bg-blue-600 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="font-medium text-sm">Kode Bantu</span>
            </a>

            <!-- Jurnal Umum -->
            <a href="/jurnalumum" class="flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300 ease-in-out rounded-xl">
                <div class="p-2 bg-blue-600 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <span class="font-medium text-sm">Jurnal Umum</span>
            </a>

            <!-- Buku Besar -->
            <a href="/bukubesar" class="flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300 ease-in-out rounded-xl">
                <div class="p-2 bg-blue-600 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <span class="font-medium text-sm">Buku Besar</span>
            </a>

            <!-- Buku Besar Pembantu -->
            <a href="/bukubesarpembantu" class="flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300 ease-in-out rounded-xl">
                <div class="p-2 bg-blue-600 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <span class="font-medium text-sm">Buku Besar Pembantu</span>
            </a>
 
            <!-- Dropdown Laba Rugi -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="w-full flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300 ease-in-out rounded-xl">
                    <div class="p-2 bg-blue-600 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="font-medium text-sm">Laba Rugi</span>
                    <svg class="w-5 h-5 ml-auto transform transition-transform duration-300" 
                         :class="{'rotate-180': open}" 
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     @click.away="open = false" 
                     class="pl-14 pr-4 py-2 space-y-1">
                    <a href="/pendapatan" class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 ease-in-out">
                        Pendapatan
                    </a>
                    <a href="/hpp" class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 ease-in-out">
                        HPP
                    </a>
                    <a href="/biayaoperasional" class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 ease-in-out">
                        Biaya Operasional
                    </a>
                </div>
            </div>

            <!-- Dropdown Neraca -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="w-full flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300 ease-in-out rounded-xl">
                    <div class="p-2 bg-blue-600 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="font-medium text-sm">Neraca</span>
                    <svg class="w-5 h-5 ml-auto transform transition-transform duration-300" 
                         :class="{'rotate-180': open}" 
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     @click.away="open = false" 
                     class="pl-14 pr-4 py-2 space-y-1">
                    <a href="/aktivalancar" class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 ease-in-out">
                        Aktiva Lancar
                    </a>
                    <a href="/aktivatetap" class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 ease-in-out">
                        Aktiva Tetap
                    </a>
                    <a href="/kewajiban" class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 ease-in-out">
                        Kewajiban
                    </a>
                    <a href="/ekuitas" class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 ease-in-out">
                        Ekuitas
                    </a>
                </div>
            </div>
        </nav>
    
        <!-- User Profile -->
        <div class="absolute bottom-0 w-full p-4 border-t bg-gray-50">
            <div class="flex items-center justify-between p-2 rounded-xl hover:bg-white transition-all duration-300 ease-in-out">
                <!-- Profile Picture and Info -->
                <div class="flex items-center">
                    <img src="{{ asset('images/ponidi.jpg') }}" alt="Profile" class="w-10 h-10 rounded-full border-2 border-blue-200">
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-700">Ponidi</p>
                        <p class="text-xs font-medium text-blue-500">CEO</p>
                    </div>
                </div>
                
                <!-- Logout Icon -->
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <button 
                        type="submit"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="flex items-center justify-center w-10 h-10 bg-red-50 hover:bg-red-100 text-red-600 rounded-full transition-all duration-300 ease-in-out"
                        aria-label="logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-6 0v-1m0-8v-1a3 3 0 016 0v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>