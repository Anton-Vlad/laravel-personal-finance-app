<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatementsController;
use App\Http\Controllers\TransactionsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/overview', function () {
    return Inertia::render('Overview');
})->middleware(['auth', 'verified'])->name('overview');

Route::get('/transactions', [TransactionsController::class, 'index'])->middleware(['auth', 'verified'])->name('transactions');

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/statements', [StatementsController::class, 'index'])->name('statements.index');
    Route::delete('/statements/{id}', [StatementsController::class, 'destroy'])->name('statements.destroy');
    Route::get('/statements/upload', [StatementsController::class, 'new'])->name('statements.new');
    Route::post('/statements/upload', [StatementsController::class, 'upload'])->name('statements.upload');
    Route::get('/statements/download/{id}', [StatementsController::class, 'download'])->name('statements.download');
});

Route::get('/budgets', function () {
    return Inertia::render('Budgets');
})->middleware(['auth', 'verified'])->name('budgets');

Route::get('/pots', function () {
    return Inertia::render('Pots');
})->middleware(['auth', 'verified'])->name('pots');

Route::get('/recurring-bills', function () {
    return Inertia::render('RecurringBills');
})->middleware(['auth', 'verified'])->name('recurring_bills');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
