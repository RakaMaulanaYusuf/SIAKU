@extends('main')

@section('title', 'Kelola Akun')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex overflow-hidden">
        <x-side-bar-admin></x-side-bar-admin>
        <div id="main-content" class="relative text-black font-poppins w-full h-full overflow-y-auto">
            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-black">Kelola Akun</h1>
                        <p class="text-gray-600 mt-1">Manajemen Akun Pengguna</p>
                    </div>
                    <button id="addUserBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah User
                    </button>
                </div>
            </div>

            <div class="bg-white mx-6 mt-6 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-blue-600">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Pengguna
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Role
                                </th>
                                {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Perusahaan Assigned
                                </th> --}}
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Tanggal Dibuat
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="usersTableBody">
                            @foreach($users as $user)
                            <tr class="hover:bg-gray-50" data-user-id="{{ $user->id }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($user->profile_photo)
                                                <img class="h-10 w-10 rounded-full object-cover"
                                                     src="{{ asset('storage/' . $user->profile_photo) }}"
                                                     alt="">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-br
                                                    @if($user->role === 'staff') from-green-400 to-green-500 @endif
                                                    @if($user->role === 'admin') from-yellow-400 to-yellow-500 @endif
                                                    flex items-center justify-center">
                                                    <span class="text-sm font-medium text-white">
                                                        {{ substr($user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($user->role === 'staff') bg-green-100 text-green-800 @endif
                                        @if($user->role === 'admin') bg-yellow-100 text-yellow-800 @endif
                                        ">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                {{-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($user->role === 'viewer')
                                        @if($user->assignedCompany)
                                            <div>
                                                <div class="font-medium">{{ $user->assignedCompany->name }}</div>
                                                @if($user->assignedPeriod)
                                                    <div class="text-xs text-gray-500">
                                                        Periode: {{ $user->assignedPeriod->period_name }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-red-500 text-sm">Belum di-assign</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td> --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="text-blue-600 hover:text-blue-900 edit-user-btn"
                                                data-user='@json($user)'>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        {{-- New Change Role Button --}}
                                        <button class="bg-purple-600 text-white hover:bg-purple-700 change-role-btn px-3 py-1 rounded-md text-sm font-medium"
                                                data-user='@json($user)'>
                                            Ubah Role
                                        </button>
                                        <button class="bg-blue-600 text-white hover:bg-blue-700 reset-password-btn px-3 py-1 rounded-md text-sm font-medium"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->name }}">
                                            Ubah Password
                                        </button>
                                        {{-- @if($user->role === 'viewer' && $user->assignedCompany) --}}
                                        {{-- <button class="text-orange-600 hover:text-orange-900 unassign-btn"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->name }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button> --}}
                                        {{-- @endif --}}
                                        <button class="text-red-600 hover:text-red-900 delete-user-btn"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->name }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL UNTUK ADD USER (akan memiliki field ROLE) --}}
<div id="userModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Tambah User Baru</h3>
            <form id="userForm">
                <input type="hidden" id="userId" name="user_id">
                <input type="hidden" id="formMethod" value="POST">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                    <input type="text" id="userName" name="name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="userEmail" name="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="mb-4" id="passwordField">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="userPassword" name="password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- HANYA MUNCULKAN FIELD ROLE KETIKA MENAMBAH USER BARU (method POST) --}}
                {{-- Kita akan menggunakan JavaScript untuk menentukan kapan ini ditampilkan/disembunyikan --}}
                <div class="mb-6" id="roleFieldContainer">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select id="userRole" name="role" required
                             class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Role</option>
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" id="closeModalBtn"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL UNTUK UBAH ROLE (selalu memiliki field ROLE) --}}
<div id="changeRoleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Ubah Role Pengguna</h3>
            <form id="changeRoleForm">
                <input type="hidden" id="changeRoleUserId">

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role Baru</label>
                    <select id="changeUserRole" name="role" required
                             class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Pilih Role</option>
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" id="closeChangeRoleModalBtn"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL UNTUK RESET PASSWORD (selalu memiliki field PASSWORD) --}}
<div id="resetPasswordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reset Password</h3>
            <form id="resetPasswordForm">
                <input type="hidden" id="resetUserId">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                    <input type="password" id="newPassword" name="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                    <input type="password" id="confirmPassword" name="password_confirmation" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" id="closeResetModalBtn"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userModal = document.getElementById('userModal');
    const resetPasswordModal = document.getElementById('resetPasswordModal');
    const changeRoleModal = document.getElementById('changeRoleModal'); 

    const userForm = document.getElementById('userForm');
    const resetPasswordForm = document.getElementById('resetPasswordForm');
    const changeRoleForm = document.getElementById('changeRoleForm'); 

    // Get the container for the role field in the userModal
    const roleFieldContainer = document.getElementById('roleFieldContainer');

    // Add User Button
    document.getElementById('addUserBtn').addEventListener('click', function() {
        document.getElementById('modalTitle').textContent = 'Tambah User Baru';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('userId').value = '';
        
        // Show password field and make it required for new users
        document.getElementById('passwordField').style.display = 'block';
        document.getElementById('userPassword').required = true;
        
        // Show role field and make it required for new users
        if (roleFieldContainer) {
            roleFieldContainer.style.display = 'block';
            document.getElementById('userRole').required = true;
        }

        userForm.reset(); // Reset the form fields
        userModal.classList.remove('hidden'); // Show the modal
    });

    // Edit User Buttons
    document.querySelectorAll('.edit-user-btn').forEach(button => {
        button.addEventListener('click', function() {
            const user = JSON.parse(this.dataset.user);
            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('userId').value = user.id;
            document.getElementById('userName').value = user.name;
            document.getElementById('userEmail').value = user.email;
            
            // Hide password field and make it not required for editing (unless resetting)
            document.getElementById('passwordField').style.display = 'none';
            document.getElementById('userPassword').required = false;

            // Hide role field for editing
            if (roleFieldContainer) {
                roleFieldContainer.style.display = 'none';
                document.getElementById('userRole').required = false; // Make sure it's not required if hidden
            }

            userModal.classList.remove('hidden'); // Show the modal
        });
    });

    // Change Role Buttons (NEW)
    document.querySelectorAll('.change-role-btn').forEach(button => {
        button.addEventListener('click', function() {
            const user = JSON.parse(this.dataset.user);
            document.getElementById('changeRoleUserId').value = user.id;
            document.getElementById('changeUserRole').value = user.role; 
            changeRoleModal.classList.remove('hidden');
        });
    });

    // Reset Password Buttons
    document.querySelectorAll('.reset-password-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('resetUserId').value = this.dataset.userId;
            resetPasswordForm.reset();
            resetPasswordModal.classList.remove('hidden');
        });
    });

    // Delete User Buttons
    document.querySelectorAll('.delete-user-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;

            Swal.fire({
                title: 'Hapus User?',
                text: `Apakah Anda yakin ingin menghapus user "${userName}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteUser(userId);
                }
            });
        });
    });

    // Unassign Company Buttons
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

    // Close Modal Buttons
    document.getElementById('closeModalBtn').addEventListener('click', () => {
        userModal.classList.add('hidden');
    });

    document.getElementById('closeResetModalBtn').addEventListener('click', () => {
        resetPasswordModal.classList.add('hidden');
    });

    document.getElementById('closeChangeRoleModalBtn').addEventListener('click', () => { 
        changeRoleModal.classList.add('hidden');
    });

    // Form Submissions
    userForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(userForm);
        const method = document.getElementById('formMethod').value;
        const userId = document.getElementById('userId').value;

        if (method === 'POST') {
            createUser(formData);
        } else {
            updateUser(userId, formData);
        }
    });

    resetPasswordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(resetPasswordForm);
        const userId = document.getElementById('resetUserId').value;
        resetPassword(userId, formData);
    });

    changeRoleForm.addEventListener('submit', function(e) { 
        e.preventDefault();
        const formData = new FormData(changeRoleForm);
        const userId = document.getElementById('changeRoleUserId').value;
        updateUserRole(userId, formData);
    });

    // API Functions (unchanged from your previous code and still correct)
    function createUser(formData) {
        fetch('/admin/users', {
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
    }

    function updateUser(userId, formData) {
        formData.append('_method', 'PUT');

        fetch(`/admin/users/${userId}`, {
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
    }

    function updateUserRole(userId, formData) { 
        formData.append('_method', 'PUT'); 

        fetch(`/admin/users/${userId}/update-role`, { 
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
    }

    function deleteUser(userId) {
        fetch(`/admin/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Terhapus!', data.message, 'success')
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

    function resetPassword(userId, formData) {
        formData.append('_method', 'PUT');

        fetch(`/admin/users/${userId}/reset-password`, {
            method: 'POST', // Use POST for PUT method override
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Berhasil!', data.message, 'success');
                resetPasswordModal.classList.add('hidden');
            } else {
                showErrors(data.errors);
            }
        })
        .catch(error => {
            Swal.fire('Error!', 'Terjadi kesalahan', 'error');
            console.error('Error:', error);
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
@endsection