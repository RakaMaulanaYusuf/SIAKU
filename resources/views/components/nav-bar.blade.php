{{-- Current Company Indicator --}}
<div>
    @if(auth()->user()->activeCompany)
        <div class="sticky top-0 bg-blue-700 text-white py-3 px-6 flex justify-between items-center z-50 shadow-md">
            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-200" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <span class="text-blue-100 text-sm">Perusahaan</span>
                        <p class="font-medium">{{ auth()->user()->activeCompany->name }}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-200" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <span class="text-blue-100 text-sm">Periode Aktif</span>
                        <p class="font-medium">{{ auth()->user()->activePeriod?->period_month }} {{ auth()->user()->activePeriod?->period_year }}</p>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('listP') }}" 
               class="flex items-center space-x-2 bg-blue-700 bg-opacity-10 hover:bg-opacity-20 px-4 py-2 rounded-lg transition-all duration-200 group">
                <span class="text-sm font-medium">Ganti Perusahaan</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:translate-x-1 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>
    @endif
</div>