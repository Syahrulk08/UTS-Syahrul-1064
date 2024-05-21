<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Firebase\JWT\JWT; 



class GoogleController extends Controller 
{
    public function callback() 
    {
        try {
            $user = Socialite::driver('google')->user();

            $userExist = User::where('oauth_id', $user->getId())->where('oauth_type', 'google')->first();
    
            if($userExist) {
                $payload = [
                    'email' => $userExist['email'],
                    'role' => $userExist['role'],
                    'iat' => now()->timestamp,
                    'exp' => now()->timestamp + 7200
                ];
            
                $jwt = JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');

                return response()->json([
                    "data" => [
                        'msg' => 'Login Succesfull',
                        'bearer' => "Bearer {$jwt}"
                    ]
                ]);

            } else {
                $newUser = User::create([
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'oauth_id' => $user->getId(),
                    'oauth_type' => 'google',
                ]);

                $payload = [
                    'email' => $newUser['email'],
                    'role' => $newUser['role'],
                    'iat' => now()->timestamp,
                    'exp' => now()->timestamp + 7200
                ];
            
                $jwt = JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');

                return response()->json([
                    "data" => [
                        'msg' => 'Daftar Succesfull',
                        'bearer' => "Bearer {$jwt}"
                    ]
                ]);
            }
        } catch (Exception $th) {
            return response()->json([
                'msg' => $th->getMessage() 
            ], 400);
        }
    
    }

        
        
}
