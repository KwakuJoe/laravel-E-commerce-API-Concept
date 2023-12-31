<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
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

// tasks
// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::apiResource('/tasks', TaskController::class);
//     Route::get('/products', [ProductController::class,'index']);
// });

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
    Route::post('/update-product/{id}', 'updateProduct');
    Route::post('/update-product-image/{image_id}/{product_id}', 'updateProductImage');
    Route::delete('/delete-product/{product_id}', 'deleteProduct');
});

// categories
Route::middleware(['auth:sanctum'])->controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'index');
    Route::get('/category/{id}', 'showCategory');
    Route::post('/create-category', 'createCategory');
    Route::post('/update-category/{id}', 'updateCategory');
    Route::delete('/delete-product/{product_id}', 'deleteCategory');
});

// orders
Route::middleware(['auth:sanctum'])->controller(OrderController::class)->group(function () {
    Route::post('/create-order', 'createOrder');
    Route::get('/orders', 'index');
    Route::post('/update-order-status/{id}', 'updateOrderStatus');
    Route::get('/show-order/{id}', 'showOrder');

});




// projecs
// Route::get('/get-all', [ProjectController::class, 'index']);
// Route::get('/get-all/{id}', [ProjectController::class, 'getPhoneById']);
// Route::get('/connect-external-api', [ProjectController::class, 'connetExternalApi']);
// Route::post('/postHttp', [ProjectController::class, 'postHttp']);
// Route::get('/connect-external-api-http', [ProjectController::class, 'connectHttp']);
// Route::post('/post-external-api', [ProjectController::class, 'postToExternalApi']);


