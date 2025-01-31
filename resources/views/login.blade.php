@extends('main')

@section('title', 'Login')

@section('page')
<div class="min-h-screen flex items-center justify-center px-4 bg-gradient-to-br from-blue-50 to-blue-100" style="background-image: url('{{ asset('images/Background.jpg') }}'); background-size: cover; background-position: center;">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 space-y-6">
        <!-- Logo dan Header -->
        <div class="flex flex-col items-center justify-center space-y-2">
            <img src="{{ asset('images/Logo.png') }}" alt="SIAKU Logo" class="w-40 h-40">
            <p class="text-black text-lg">Sistem Informasi Akuntansi</p>
        </div>

        @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form class="space-y-4" method="POST" action="{{ route('login.post') }}">   
            @csrf         
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Email</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="{{ old('username') }}"
                    class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Masukkan email">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Masukkan password">
            </div>

            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    id="remember" 
                    name="remember"
                    {{ old('remember') ? 'checked' : '' }}
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-sm text-gray-700">
                    Ingat saya
                </label>
            </div>

            <button 
                type="submit"
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 ease-in-out transform hover:-translate-y-0.5">
                Login
            </button>
        </form>
    </div>
</div>
@endsection