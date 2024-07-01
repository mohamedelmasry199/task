<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'verifyCode']]);
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'mobile_number' => 'required|string',
                'password' => 'required|string|min:6',
            ]);

            $credentials = $request->only('mobile_number', 'password');
            if (!Auth::validate($credentials)) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 401);
            }

            $user = User::where('mobile_number', $request->mobile_number)->first();

            if (!$user->is_verified) {
                $user->generateCode();
                return response()->json([
                    'message' => 'Verification code sent',
                    'user_id' => $user->id
                ]);
            }

            $token = Auth::attempt($credentials);
            return response()->json([
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function verifyCode(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|integer',
                'code' => 'required|integer',
            ]);

            $user = User::find($request->user_id);

            if (!$user || !$user->verifyCode($request->code)) {
                return response()->json([
                    'message' => 'Invalid code',
                ], 401);
            }

            $user->is_verified = true;
            $user->save();

            $token = Auth::login($user);

            return response()->json([
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'user_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'mobile_number' => 'required|string|unique:users',
                'password' => 'required|string|min:6',
            ]);

            $user = User::create([
                'user_name' => $request->user_name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
                'password' => Hash::make($request->password),
            ]);

            $credentials = $request->only('mobile_number', 'password');
            $token = Auth::attempt($credentials);

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'user' => Auth::user(),
            'authorization' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        return response()->json([
            'user' => $user,
        ]);
    }
}


















//K19UUH658QYP81AQQ42N664N
