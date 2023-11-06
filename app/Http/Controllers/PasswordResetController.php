<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\PasswordResetTokens;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use App\Notifications\PasswordResetSuccessNotification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{


    public function resetPassword($token, $email, Request $request){

        if (!$request->hasValidSignature()) {

            return view('404.404');

        }

        return view('password.reset-password', [
            'token' => $token,
            'email' => $email
        ]);

    }

    public function updatePassword(UpdatePasswordRequest $request){


        try  {

            $validatedData = $request->validated();

            $user = User::where('email', $validatedData['email'])->first();

            if(!$user){

                return redirect()->back()->with('failed', 'User with this email, not found :(?');

            }

            $user->password = Hash::make($validatedData['password']);
            $user->save();

            $name = $user -> name;

            $delay = now()->addMinutes(1);

            // Send the email
            $user->notify((new PasswordResetSuccessNotification($name))->delay($delay));

            return redirect()->back()->with('status', 'Your password has been reset. You can now log in with your new password.');


        } catch(\Exception $e){

            return redirect()->back()->with('failed', 'Error, trying to update password, try again?');

        }

    }

}
