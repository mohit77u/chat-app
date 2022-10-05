<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $input = $request->validate([
            'name'                       => 'required',
            'email'                      => 'required|unique:users|email',
            'password'                   => 'required',
            'password_confirmation'      => 'required|same:password',
        ]);

        // bycrypt password
        $input['password'] = Hash::make($input['password']);

        // store in db table
        User::create([
            'name'      => $input['name'],
            'email'     => $input['email'],
            'password'  => $input['password'],
        ]);

        // return response
        return response([
            'message' => 'User successfully registered',
        ], 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'                      => 'required|email',
            'password'                   => 'required',
        ]);

        $login = $request->only('email', 'password');

        if (!Auth::attempt($login)) {
            return response([
                'errors' => [
                    'email' => ['Email does not exist or verified'],
                    'password' => ['Password incorrect/invalid'],
                ]
            ], 401);
        }

        /**
         * @var User $user
         */
        $user = Auth::user();
        $token = $user->createToken($user->name);

        return response([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'token' => $token->accessToken,
            'token_expires_at' => $token->token->expires_at,
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $email = $request->email;

        if(User::where('email', $email)->doesntExist()){
            return response([
                'message'=>'User with this email does not exists!'
            ], 200);
        }
        $token = Str::random(100);

        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => now()->addHours(6)
        ]);

        $data = [
            'token' =>$token,
            'user'  => User::where('email', $email)->first(),
        ];

        // Send Mail
        Mail::to($email)->send(new ForgotPasswordMail($data));

        return response([
            'message' => 'Check your email.'
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        $token = $request->token;
        $passwordRest = DB::table('password_resets')->where('token', $token)->first();

        // Verify
        if(!$passwordRest)
        {
            return response(['message' => 'Token Not Found.'], 200);
        }

        // Validate exipire time
        if(!$passwordRest->created_at >= now())
        {
            return response([
                'message' => 'Token has expired.'
            ], 200);
        }

        $user = User::where('email', $passwordRest->email)->first();

        if(!$user)
        {
            return response([
                'message' => 'User does not exists.'
            ], 200);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_resets')->where('token', $token)->delete();;

        return response([
            'message'=>'Password Successfully Updated.'
        ], 200);
    }
}
