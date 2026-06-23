<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InvoiceController::class, 'create'])->name('invoices.create');
Route::get('/bootstrap', [InvoiceController::class, 'bootstrap'])->name('invoices.bootstrap');

Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');

Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
