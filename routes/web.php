<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\ZohoValidAccessToken;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'user' => \Illuminate\Support\Facades\Auth::user(),
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('index', function () {
    return Inertia::location('/zoho/list');
    //return Inertia::render('Index', ['user' => \Illuminate\Support\Facades\Auth::user()]);
})->middleware(['auth', 'verified'])->name('index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', ZohoValidAccessToken::class])->get(
    '/zoho/login', [\App\Http\Controllers\ZohoController::class, 'login']
);
Route::middleware(['auth', ZohoValidAccessToken::class])->get(
    '/zoho/list', [\App\Http\Controllers\ZohoController::class, 'list']
)->name('list');
Route::middleware(['auth', ZohoValidAccessToken::class])->get(
    '/zoho/get-tokens', [\App\Http\Controllers\ZohoController::class, 'getTokens']
);
Route::middleware(['auth', ZohoValidAccessToken::class])->get(
    '/zoho/create-account', [\App\Http\Controllers\ZohoController::class, 'createAccountDeal']
)->name('create');

Route::middleware(['auth', ZohoValidAccessToken::class])->post(
    '/zoho/save-account', [\App\Http\Controllers\ZohoController::class, 'saveAccountDeal']
)->name('save');
require __DIR__ . '/auth.php';
