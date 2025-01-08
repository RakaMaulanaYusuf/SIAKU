<div>
    <div class="fixed left-0 top-0 w-72 h-full bg-white shadow-lg rounded-r-3xl">
        <!-- Logo - Centered dengan padding yang lebih baik -->
        <div class="flex justify-center items-center py-8">
            <img src="{{ asset('images/Logo.png') }}" alt="SIAKU" class="w-36 h-auto">
        </div>
    
        <!-- Menu Items dengan spacing yang lebih baik -->
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
    
            <!-- Jurnal Umum -->
            <a href="/jurnalumum" class="flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300 ease-in-out rounded-xl">
                <div class="p-2 bg-blue-600 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
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
 
            <!-- Neraca Lajur -->
            <a href="/neracalajur" class="flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300 ease-in-out rounded-xl">
                <div class="p-2 bg-blue-600 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <span class="font-medium text-sm">Neraca Lajur</span>
            </a>
 
            <!-- Dropdown Laporan -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="w-full flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300 ease-in-out rounded-xl">
                    <div class="p-2 bg-blue-600 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="font-medium text-sm">Laporan</span>
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
                    <a href="/labarugi" class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 ease-in-out">
                        Laba Rugi
                    </a>
                    <a href="/neraca" class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 ease-in-out">
                        Neraca
                    </a>
                </div>
            </div>
 
            <!-- Dropdown Kode -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="w-full flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300 ease-in-out rounded-xl">
                    <div class="p-2 bg-blue-600 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        </svg>
                    </div>
                    <span class="font-medium text-sm">Kode</span>
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
                    <a href="/kodeakun" class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 ease-in-out">
                        Kode Akun
                    </a>
                    <a href="/kodebantu" class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 ease-in-out">
                        Kode Bantu
                    </a>
                </div>
            </div>
        </nav>
    
        <!-- User Profile -->
        <div class="absolute bottom-0 w-full p-4 border-t bg-gray-50">
            <div class="flex items-center p-2 rounded-xl hover:bg-white transition-all duration-300 ease-in-out">
                <img src="{{ asset('images/ponidi.jpg') }}" alt="Profile" class="w-10 h-10 rounded-full border-2 border-blue-200">
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-700">Ponidi</p>
                    <p class="text-xs font-medium text-blue-500">CEO</p>
                </div>
            </div>
        </div>
    </div>
 </div>