@extends('main')

@section('title', 'Buku Besar')

@section('page')
<div class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex overflow-hidden">
        <x-side-bar-menu></x-side-bar-menu>
        <div id="main-content" class="relative text-black ml-72 font-poppins w-full h-full overflow-y-auto">
            <div class="bg-white p-6 mx-6 mt-6 rounded-xl shadow-sm">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Buku Besar</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection