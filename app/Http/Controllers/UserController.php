<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $validated = $validator->validated();

        if(Auth::attempt($validated)) {
            
            $payload = [
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'role' => Auth::user()->role,
                'iat' => now()->timestamp,
                'exp' => now()->timestamp + 7200
            ];

            $jwt = JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');

            return response()->json([
                "data" => [
                    'msg' => 'Login successfully',
                    'token' => "Bearer {$jwt}"
                ]
            ], 200);
        }

        return response()->json([
            'msg' => 'Invalid email or password'
        ], 401);
    }

    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $validated = $validator->validated();

        if(User::where('email', $validated['email'])->exists()) {
            return response()->json([
                'msg' => 'Email already exists'
            ], 400);
        }
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'])
        ]);

        return response()->json([
            "data" => [
                'msg' => 'Register successfully',
                'data' => $user
            ]
        ], 201);
    }       
}
