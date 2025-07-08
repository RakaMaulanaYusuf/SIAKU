{{-- @extends('main')

@section('title', 'Assign Perusahaan')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex overflow-hidden">
        <x-side-bar-admin></x-side-bar-admin>
        
        <div id="main-content" class="relative text-black font-poppins w-full h-full overflow-y-auto">
            <!-- Header Box -->
            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-black">Assign Perusahaan ke Viewer</h1>
                        <p class="text-gray-600 mt-1">Kelola assignment perusahaan untuk akun viewer</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                <!-- Assignment Form -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold">Assign Perusahaan Baru</h2>
                        <p class="text-sm text-gray-600 mt-1">Pilih viewer dan perusahaan untuk di-assign</p>
                    </div>
                    <div class="p-6">
                        <form id="assignForm">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Viewer</label>
                                <select id="viewerSelect" name="viewer_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Pilih Viewer...</option>
                                    @foreach($viewers as $viewer)
                                    <option value="{{ $viewer->id }}" 
                                            data-assigned="{{ $viewer->assignedCompany ? 'true' : 'false' }}"
                                            data-company="{{ $viewer->assignedCompany ? $viewer->assignedCompany->name : '' }}">
                                        {{ $viewer->name }} ({{ $viewer->email }})
                                        @if($viewer->assignedCompany)
                                            - Sudah assigned ke {{ $viewer->assignedCompany->name }}
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                                <p id="viewerWarning" class="text-sm text-orange-600 mt-1 hidden">
                                    Viewer ini sudah di-assign ke perusahaan lain
                                </p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Perusahaan</label>
                                <select id="companySelect" name="company_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Pilih Perusahaan...</option>
                                    @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Periode</label>
                                <select id="periodSelect" name="period_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Pilih periode terlebih dahulu...</option>
                                </select>
                            </div>
                            
                            <button type="submit" 
                                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                                Assign Perusahaan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Current Assignments -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold">Assignment Saat Ini</h2>
                        <p class="text-sm text-gray-600 mt-1">Daftar viewer yang sudah di-assign</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            @forelse($viewers->where('assignedCompany') as $viewer)
                            <div class="border rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-yellow-600">
                                                {{ substr($viewer->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="ml-3">
                                            <div class="font-medium text-gray-900">{{ $viewer->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $viewer->email }}</div>
                                        </div>
                                    </div>
                                    <button class="text-red-600 hover:text-red-800 unassign-btn" 
                                            data-user-id="{{ $viewer->id }}"
                                            data-user-name="{{ $viewer->name }}"
                                            title="Unassign">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                </div>
                                
                                <div class="mt-3 pl-13">
                                    <div class="text-sm">
                                        <span class="font-medium text-gray-700">Perusahaan:</span>
                                        <span class="text-gray-900">{{ $viewer->assignedCompany->name }}</span>
                                    </div>
                                    @if($viewer->assignedPeriod)
                                    <div class="text-sm mt-1">
                                        <span class="font-medium text-gray-700">Periode:</span>
                                        <span class="text-gray-900">{{ $viewer->assignedPeriod->period_name }}</span>
                                        <span class="text-xs text-gray-500 ml-2">
                                            ({{ $viewer->assignedPeriod->period_month }} {{ $viewer->assignedPeriod->period_year }})
                                        </span>
                                    </div>
                                    @endif
                                    <div class="text-xs text-gray-500 mt-2">
                                        Assigned pada: {{ $viewer->updated_at->format('d M Y H:i') }}
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500">Belum ada viewer yang di-assign</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
                <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $viewers->count() }}</div>
                    <div class="text-sm text-gray-600">Total Viewers</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $viewers->whereNotNull('assigned_company_id')->count() }}</div>
                    <div class="text-sm text-gray-600">Sudah Di-assign</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $viewers->whereNull('assigned_company_id')->count() }}</div>
                    <div class="text-sm text-gray-600">Belum Di-assign</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewerSelect = document.getElementById('viewerSelect');
    const companySelect = document.getElementById('companySelect');
    const periodSelect = document.getElementById('periodSelect');
    const assignForm = document.getElementById('assignForm');
    const viewerWarning = document.getElementById('viewerWarning');
    
    // Handle viewer selection
    viewerSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const isAssigned = selectedOption.dataset.assigned === 'true';
        const companyName = selectedOption.dataset.company;
        
        if (isAssigned && companyName) {
            viewerWarning.textContent = `Viewer ini sudah di-assign ke ${companyName}. Assignment baru akan mengganti yang lama.`;
            viewerWarning.classList.remove('hidden');
        } else {
            viewerWarning.classList.add('hidden');
        }
    });
    
    // Handle company selection
    companySelect.addEventListener('change', function() {
        const companyId = this.value;
        periodSelect.innerHTML = '<option value="">Loading...</option>';
        
        if (companyId) {
            fetch(`/admin/companies/${companyId}/periods`)
                .then(response => response.json())
                .then(periods => {
                    periodSelect.innerHTML = '<option value="">Pilih Periode...</option>';
                    periods.forEach(period => {
                        const option = document.createElement('option');
                        option.value = period.id;
                        option.textContent = period.period_name;
                        periodSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    periodSelect.innerHTML = '<option value="">Error loading periods</option>';
                    console.error('Error:', error);
                });
        } else {
            periodSelect.innerHTML = '<option value="">Pilih perusahaan terlebih dahulu...</option>';
        }
    });
    
    // Handle form submission
    assignForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(assignForm);
        
        fetch('/admin/assign-company', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Berhasil!', data.message, 'success')
                    .then(() => location.reload());
            } else {
                showErrors(data.errors);
            }
        })
        .catch(error => {
            Swal.fire('Error!', 'Terjadi kesalahan', 'error');
            console.error('Error:', error);
        });
    });
    
    // Handle unassign buttons
    document.querySelectorAll('.unassign-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            
            Swal.fire({
                title: 'Unassign Perusahaan?',
                text: `Hapus assignment perusahaan dari "${userName}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Unassign!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    unassignCompany(userId);
                }
            });
        });
    });
    
    // Helper functions
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', { 
            year: 'numeric', 
            month: 'short' 
        });
    }
    
    function unassignCompany(userId) {
        fetch(`/admin/users/${userId}/unassign`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Berhasil!', data.message, 'success')
                    .then(() => location.reload());
            } else {
                Swal.fire('Error!', data.message, 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error!', 'Terjadi kesalahan', 'error');
            console.error('Error:', error);
        });
    }
    
    function showErrors(errors) {
        let errorMessage = '';
        for (let field in errors) {
            errorMessage += errors[field].join('<br>') + '<br>';
        }
        Swal.fire('Validation Error!', errorMessage, 'error');
    }
});
</script>
@endsection --}}