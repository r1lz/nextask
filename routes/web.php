<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (app()->environment('production')) {
        return redirect('/dashboard');
    }
    
    return redirect('/docs/api');
});

Route::get('/dashboard', function () {
    // Jika user sudah memilih bahasa (via query atau cookie), hormati pilihannya
    if (request()->query('lang') || request()->cookie('docs_lang')) {
        $lang = request()->query('lang') ?? request()->cookie('docs_lang');
        $lang = in_array($lang, ['en', 'id']) ? $lang : 'en';
    } else {
        // Deteksi dari Accept-Language header browser
        $acceptLang = request()->header('Accept-Language', '');
        // Cek apakah header mengandung 'id' (Indonesian) sebagai bahasa utama
        $lang = str_contains(strtolower($acceptLang), 'id') ? 'id' : 'en';
    }
    return view('dashboard', compact('lang'));
});
