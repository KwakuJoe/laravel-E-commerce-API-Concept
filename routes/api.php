<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



// Current logged user
Route::middleware('auth:sanctum')->get('/auth/user', function (Request $request) {
    return $request->user();
});

// auth routes
Route::controller(AuthController::class)->group(function () {
    Route::post('auth/login', 'login');
    Route::post('auth/register','register');
    Route::post('auth/forget-password', 'forgetPassword');
    Route::get('auth/logout', 'logout')->middleware('auth:sanctum');
    Route::get('auth/send-email-verification/{email}', 'sendEmailVerifcation');

});

// products
Route::middleware(['auth:sanctum'])->controller(ProductController::class)->group(function () {
    Route::get('/products', 'index');
    Route::get('/product/{id}', 'showProduct');
    Route::post('/create-product', 'createProduct');
    Route::patch('/update-product/{id}', 'updateProduct');
    Route::post('/update-product-image/{image_id}/{product_id}', 'updateProductImage');
    Route::delete('/delete-product/{product_id}', 'deleteProduct');
});







