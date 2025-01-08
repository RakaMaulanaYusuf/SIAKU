<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/jurnalumum', function () {
    return view('jurnalumum');
});

Route::get('/jurnalumumD', function () {
    return view('jurnalumumDetail');
});

Route::get('/bukubesar', function () {
    return view('bukubesar');
});

Route::get('/bukubesarD', function () {
    return view('bukubesarDetail');
});