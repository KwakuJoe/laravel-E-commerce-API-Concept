<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {

    $token = 'djjdjdjdjd88383';
    $email = 'dkdkd@hhdd.com';
    return view('welcome', [
        'token' => $token,
        'email' => $email
    ]);
});

Route::get('auth/verify-email-view')
->name('emailVerifiedView');

Route::get('/404', function () {
    return view('404.404');
})
->name('404');

Route::get('/auth/verify-email-view/{token}', function (Request $request, $token,) {

    if (!$request->hasValidSignature()) {
        // abort(401);
        return view('verify.email-verified-error');
    }

        // decrypt token
        $decrypted = Crypt::decryptString($token);

        // return view('verify.email-verified');
        $user = User::where('email', $decrypted)->first();

        // if no user found direct 404;
        if(!$user){
            return view('404.404');
        }

        // empty rember me token
        $user->email_verification_token = null;

        // filed verified at with current time
        $user->email_verified_at = Carbon::now();

        $user->save();

        // show this page
        return view('verify.email-verified');

    // ...
})->name('emailVerifiedView');


// Route::post('/auth/forget-password', [PasswordResetController::class,'forgetPassword']);
Route::post('/auth/reset-password', [PasswordResetController::class,'updatePassword'])->name('updatePassword');
Route::get('/auth/reset-password/{token}/{email}', [PasswordResetController::class,'resetPassword'])->name('resetPassword');

