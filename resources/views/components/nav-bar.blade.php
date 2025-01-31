<div>
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
</div>