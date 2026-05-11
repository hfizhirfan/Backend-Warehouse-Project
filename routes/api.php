<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\UomController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\WarehouseMappingController;
use App\Http\Controllers\Api\BillOfMaterialController;
use App\Http\Controllers\Api\BundlingController;
use App\Http\Controllers\Api\ReturnController;
use App\Http\Controllers\Api\StockOpnameController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| 🔓 PUBLIC (NO LOGIN)
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);


/*
|--------------------------------------------------------------------------
| 🔐 AUTHENTICATED (SEMUA USER LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);

    // dashboard semua role
    Route::get('/dashboard', [ProductController::class, 'dashboard']);



    Route::middleware(['role:super_admin'])->group(function () {

        Route::apiResource('brands', BrandController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('uoms', UomController::class);

        Route::apiResource('products', ProductController::class);

        // 🔒 FULL CRUD (kecuali index dipisah)
        Route::apiResource('warehouse-mappings', WarehouseMappingController::class)
            ->except(['index']);

        Route::get('/bom', [BillOfMaterialController::class, 'index']);
        Route::post('/bom', [BillOfMaterialController::class, 'store']);

        Route::post('/bundling/process', [BundlingController::class, 'process']);

        Route::apiResource('users', UserController::class);
    });


    Route::middleware(['role:admin,super_admin'])->group(function () {

        // ✅ READ ONLY warehouse mapping
        Route::get('/warehouse-mappings', [WarehouseMappingController::class, 'index']);

        Route::prefix('transactions')->group(function () {
            Route::get('/', [TransactionController::class, 'index']);
            Route::post('/inbound', [TransactionController::class, 'inbound']);
            Route::post('/outbound', [TransactionController::class, 'outbound']);
            Route::post('/return', [TransactionController::class, 'return']);
            Route::post('/adjustment', [TransactionController::class, 'adjustment']);
            Route::get('/stock/{productId}', [TransactionController::class, 'stock']);
            Route::post('/scan-outbound', [TransactionController::class, 'scanOutbound']);
        });

        Route::get('/report/outbound', [TransactionController::class, 'reportOutbound']);

        Route::apiResource('orders', OrderController::class);
        Route::get('/orders/waybill/{waybill}', [OrderController::class, 'byWaybill']);
        Route::post('/scan-order', [OrderController::class, 'scanOrder']);

        Route::get('/returns', [ReturnController::class, 'index']);
        Route::get('/returns/today', [ReturnController::class, 'today']);
        Route::post('/returns', [ReturnController::class, 'store']);

        Route::get('/opname', [StockOpnameController::class, 'index']);
        Route::post('/opname', [StockOpnameController::class, 'store']);
    });


    Route::middleware(['role:user,admin,super_admin'])->group(function () {

        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/brands', [BrandController::class, 'index']);
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/uoms', [UomController::class, 'index']);
    });

});
