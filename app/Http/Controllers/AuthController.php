<?php

namespace App\Http\Controllers;

use App\Events\EmailVerification;
use App\Events\SendEmailVerificationEvent;
use App\Events\UserRegistered;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\UserRegisterdMail;
use App\Models\PasswordResetTokens;
use App\Models\User;
use App\Notifications\AccountCreatedSuccessfullNotification;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\PasswordResetNotification;
use Event;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Crypt;


class AuthController extends Controller
{
    public function login(LoginRequest $request){

        try {

            $credentials = $request->validated();

            // check creditial is false
            if(!Auth::attempt($credentials)){
                return response()->json([
                    "status"=> "failed",
                    "message"=> "Invalid credential, please try again",
                    "data" => null
                ], 200);
            }
            //else find user and generate tokens give am
            $user = User::where("email", $credentials["email"])->first();

            // create token for user
            $token = $user->createToken('api_token')->plainTextToken;

            return response()->json([
                'status'=> 'success',
                'message'=> 'Authenticates Succesfully :)',
                'token' => [
                    'token'=> $token,
                    'token_type' => 'Bearer',
                ]
            ], 200);


        }catch(\Exception $e){

            return response()->json([
                'status'=> 'failed',
                'message'=> $e->getMessage(),
                'token' => [
                    'token'=> null,
                    'token_type' => null,
                ]
            ], 200);
        }
    }



    public function register(RegisterRequest $request){

        try{

            //
            $registerData = $request->validated();

            // create user
            $user = User::create($registerData);

            // create token for user
            $token = $user->createToken('api_token')->plainTextToken;

            // send email first and asign token to user
            $email_token = Crypt::encryptString($user->email);

            // update this fields
            $user->email_verification_token = $token;
            $user->save();

            // generate a signe url
            $url =  URL::temporarySignedRoute(
                'emailVerifiedView', now()->addMinutes(30),
                ['token' => $email_token],);

            // send notification
            $user->notify(new AccountCreatedSuccessfullNotification($user, $url));
            // SendEmailVerificationEvent::dispatch($user, $url); // if im send email template

            return response()->json([
                'status'=> 'success',
                'message'=> 'Account created Succesfully :)',
                'token' => [
                    'token'=> $token,
                    'token_type' => 'Bearer',
                ]
            ], 200);

        }catch(\Exception $e){

            return response()->json([
                'status'=> 'failed',
                'message'=> $e->getMessage(),
                'token' => [
                    'token'=> null,
                    'token_type' => null,
                ]
            ], 200);

        }


    }

    public function sendEmailVerifcation( $email){


        try{

            // find user with the passed email
            $user = User::where('email', $email)->first();

            if(!$user){
                return response()->json([
                    'status'=> 'failed',
                    'message' => 'User is not authenticated or not found',
                    'user' => null
                ], 404);
            }

            // send email first and asign token to user
            $token = Crypt::encryptString($user->email);

            // update this fields
            $user->email_verification_token = $token;
            $user->save();

            // generate a signe url
            $url =  URL::temporarySignedRoute(
                'emailVerifiedView', now()->addMinutes(30),
                ['token' => $token],);

            // send email to user
            // EmailVerification::dispatch($user, $url);
            $user->notify(new EmailVerificationNotification($user, $url));


            //generate toke for
            return response()->json([
                'status'=> 'success',
                'message'=> 'Email as been sent to this address ' .$email. 'Visit to  confirm',
                'url' => $url,
                'token' => $token,
            ], 200);

        }catch(\Exception $exception){

            return response()->json([
                'status'=> 'failed',
                'message'=> $exception->getMessage(),
                'url' => $url,
                'token' => $token,
            ], 200);
        }

    }


    public function verifyEmail( Request $request, $token, ){

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
    }


    public function forgetPassword(ForgetPasswordRequest $request){

        try {

            $validated = $request->validated();
            // find user email in db

            $user = User::where("email", $validated["email"])->first();

            if(!$user){

                return response()->json([
                    'status' => 'failed',
                    'message'=> 'This email does not associate with any account',
                    'data' => null
                ], 404);

            }

            // generate expiry url link with token and users/email
            $random = Str::random(10);
            $token = Crypt::encryptString($random);
            $dateTime = Carbon::now();


            $url =  URL::temporarySignedRoute(
                'resetPassword', now()->addMinutes(30),
                [
                    'token' => $token,
                    'email' => $user->email
                ]
            );

            $message  = [
                'title' => 'Password Reset',
                'message' =>'Hello '. $user->name . ' The following url will direct to the page where you would rest your password',
                'url' => $url
            ];

            // Send the email
            $user->notify(new PasswordResetNotification($message));

            $passResetToken = PasswordResetTokens::updateOrCreate(
                ['email'=> $validated['email']],
                [
                    'email' => $validated['email'],
                    'token' => $token,
                    'created_at' => $dateTime,
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Password rest link sent to your email',
                'data' => $passResetToken
            ], 200);


            // $user->password = bcrypt($validated['password']);


        } catch (\Exception $e) {

            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'data' => null
            ], 200);
        }
    }




    public function logout(Request $request){

        // logout

        try{

            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logout successfully :)',

            ], 200);


        }catch(\Exception $exception){

            return response()->json([
                'status' => 'failed',
                'message' => $exception->getMessage(),
            ], 200);

        }
    }

}
