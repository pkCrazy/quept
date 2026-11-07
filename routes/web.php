<?php

use App\Livewire\Debts\Index as DebtsIndex;
use App\Livewire\Debts\Show as DebtShow;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('debts', DebtsIndex::class)->name('debts.index');
    Route::livewire('debts/{debt}', DebtShow::class)->name('debts.show');
});

require __DIR__.'/settings.php';
