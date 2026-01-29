<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('commission-report');
});

Route::get('/commission-report', function () {
    return view('commission-report');
})->name('commission-report');

Route::get('/top-distributors', function () {
    return view('top-distributors');
})->name('top-distributors');
