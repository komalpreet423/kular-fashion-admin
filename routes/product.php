<?php

use App\Http\Controllers\ProductBarcodeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/get-products', [ProductController::class, 'getProducts'])->name('get.products');
Route::post('/products/colors/upload-image', [ProductController::class, 'uploadColorImage'])->name('products.colors.upload-image');

Route::middleware(['auth', 'auth.session'])->group(function () {
    Route::get('products/create/step-1', [ProductController::class, 'productStep1'])->name('products.create.step-1');
    Route::post('products/create/step-1', [ProductController::class, 'saveStep1'])->name('products.save-step-1');
    Route::get('products/create/step-2', [ProductController::class, 'productStep2'])->name('products.create.step-2');
    Route::post('products/create/step-2', [ProductController::class, 'saveStep2'])->name('products.save-step-2');
    Route::get('products/create/step-3', [ProductController::class, 'productStep3'])->name('products.create.step-3');
    Route::post('add-variant', [ProductController::class, 'addVariant'])->name('add.variant');
    Route::get('products/remove-variant/{colorId}', [ProductController::class, 'removeVariant'])->name('products.remove-variant');

    Route::put('products/update/step-1/{product}', [ProductController::class, 'updateStep1'])->name('products.update-step-1');
    Route::get('products/edit/step-2/{product}', [ProductController::class, 'editStep2'])->name('products.edit.step-2');
    Route::put('products/update/step-2/{product}', [ProductController::class, 'updateStep2'])->name('products.update-step-2');

    // Bulk Edit Product
    Route::post('products/bulk-edit', [ProductController::class, 'bulkUpdate'])->name('products.bulk-edit');

    Route::get('products/edit/web-configuration/{product}', [ProductController::class, 'editWebConfigration'])->name('products.edit.web-configuration');
    Route::put('products/update/web-configuration/{product}', [ProductController::class, 'updateWebConfigration'])->name('products.update.web-configuration');

    // Generate product barcodes
    Route::get('/products/print-barcodes', [ProductBarcodeController::class, 'index'])->name('products.print-barcodes');
    Route::get('/export/csv', [ProductController::class, 'downloadExcel'])->name('export.csv');

});