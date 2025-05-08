<?php

namespace App\Http\Controllers;

use App\Models\User;
use Hash;
use Http;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'username' => 'required|string|max:255',
            'phonenumber' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            // 'captcha_token' => 'required|string',
            'role' => 'in:user,merchant,admin'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        // $captchaResponse = $request->input('captcha_token');
        // $verify = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
        //     'secret' => env('RECAPTCHA_SECRET_KEY'),
        //     'response' => $captchaResponse,
        // ]);

        // if (!optional($verify->json())['success']) {
        //     return response()->json(['error' => 'CAPTCHA verification failed'], 422);
        // }

        $user = User::create([
            'username' => $request->username,
            'phonenumber' => $request->phonenumber,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user',
        ]);


        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
{
    $login = $request->input('login'); 
    $password = $request->input('password');


    $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    $credentials = [
        $fieldType => $login,
        'password' => $password,
    ];

    if (!$token = JWTAuth::attempt($credentials)) {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    return response()->json([
        'message' => 'Login successful',
        'token' => $token,
        'user' => auth()->user()
    ]);
}
}
