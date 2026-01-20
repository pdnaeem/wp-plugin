<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalculationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmissionFunctionController;
use App\Http\Controllers\GeometryController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubstanceController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('/install', [InstallController::class, 'index'])->name('install.index');
Route::post('/install', [InstallController::class, 'store'])->name('install.store');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/geometry', [GeometryController::class, 'index'])->name('geometry.index');
    Route::get('/geometry/create', [GeometryController::class, 'create'])->name('geometry.create');
    Route::post('/geometry', [GeometryController::class, 'store'])->name('geometry.store');
    Route::get('/geometry/{geometry}', [GeometryController::class, 'show'])->name('geometry.show');

    Route::get('/weather', [WeatherController::class, 'index'])->name('weather.index');
    Route::get('/weather/create', [WeatherController::class, 'create'])->name('weather.create');
    Route::post('/weather', [WeatherController::class, 'store'])->name('weather.store');
    Route::get('/weather/{weather}', [WeatherController::class, 'show'])->name('weather.show');

    Route::get('/substances', [SubstanceController::class, 'index'])->name('substances.index');
    Route::get('/substances/create', [SubstanceController::class, 'create'])->name('substances.create');
    Route::post('/substances', [SubstanceController::class, 'store'])->name('substances.store');
    Route::get('/substances/{substance}', [SubstanceController::class, 'show'])->name('substances.show');

    Route::get('/materials', [MaterialController::class, 'index'])->name('materials.index');
    Route::post('/materials/types', [MaterialController::class, 'storeType'])->name('materials.types.store');
    Route::post('/materials/subtypes', [MaterialController::class, 'storeSubtype'])->name('materials.subtypes.store');
    Route::post('/materials', [MaterialController::class, 'storeMaterial'])->name('materials.store');

    Route::get('/emissions', [EmissionFunctionController::class, 'index'])->name('emissions.index');
    Route::get('/emissions/create', [EmissionFunctionController::class, 'create'])->name('emissions.create');
    Route::post('/emissions/manual', [EmissionFunctionController::class, 'storeManual'])->name('emissions.manual.store');
    Route::post('/emissions/fit', [EmissionFunctionController::class, 'storeFit'])->name('emissions.fit.store');
    Route::get('/emissions/{emission}', [EmissionFunctionController::class, 'show'])->name('emissions.show');

    Route::get('/calculations', [CalculationController::class, 'index'])->name('calculations.index');
    Route::get('/calculations/create', [CalculationController::class, 'create'])->name('calculations.create');
    Route::post('/calculations', [CalculationController::class, 'store'])->name('calculations.store');
    Route::get('/calculations/{calculation}', [CalculationController::class, 'show'])->name('calculations.show');

    Route::get('/calculations/{calculation}/report', [ReportController::class, 'downloadReport'])->name('calculations.report');
    Route::get('/calculations/{calculation}/export', [ReportController::class, 'downloadExport'])->name('calculations.export');
});
