<?php

use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ProductBarcodeController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\ProductTypeController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\CollectionController as CollectionApiController;
use App\Http\Controllers\Api\UserAddressController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);

Route::post('products/add-manufacture-barcode', [ProductBarcodeController::class, 'addManufactureBarcode']);
Route::post('/collections/check-name', [CollectionController::class, 'checkCollectionName']);

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [LoginController::class, 'register']);
Route::get('/brands', [BrandController::class, 'brands'])->name('brand.index');
Route::get('/departments', [DepartmentController::class, 'departments'])->name('department.index');
Route::get('/product-types', [ProductTypeController::class, 'producTypes'])->name('productType.index');
Route::get('/collections', [CollectionApiController::class, 'collections']);
Route::get('/collection/{id}', [CollectionApiController::class, 'showCollection']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('products/{product}', [ProductController::class, 'showProduct'])->name('productss.show');
    Route::post('/apply-coupon', [CouponController::class, 'applyCoupon']);

    Route::post('/addresses/create', [UserAddressController::class, 'store']);
    Route::get('/addresses', [UserAddressController::class, 'index']);
    Route::get('/addresses/default', [UserAddressController::class, 'show']);
    Route::put('/addresses/update', [UserAddressController::class, 'update']);
    Route::delete('/addresses/delete', [UserAddressController::class, 'destroy']);
});



Route::prefix('cart')->group(function () {
    Route::post('/add', [CartController::class, 'addToCart']);
    Route::get('/', [CartController::class, 'viewCart']);
    Route::put('/update', [CartController::class, 'updateCart']);
    Route::delete('/remove/{id}', [CartController::class, 'removeItem']);
    Route::delete('/clear', [CartController::class, 'clearCart']);
});
