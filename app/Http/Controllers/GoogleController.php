<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver("google")->redirect();
    }

    public function callback(){
        try{
            // Google user object dari google
            $userFromGoogle = Socialite::driver('google')->user();


            // Ambil user dari database berdasarkan email
            $userFromDatabase = User::where('email', $userFromGoogle->getEmail())->first();

            //Jika email user sudah digunakan
            if ($userFromDatabase) {
                $payload = [
                    'name'=> $userFromGoogle->getEmail(),
                    'role'=> 'user',
                    'iat'=> now()->timestamp,//waktu token di generate
                    'exp'=> now()->timestamp + 60*60*2 //waktu token expire
        
                ];
                $jwt = JWT::encode($payload,env('JWT_SECRET_KEY'),'HS256');
                return response()->json([
                    'messages'=>'Token Berhasil digenerate',
                    'name'=>$userFromGoogle->getName(),
                    'token'=>'Bearer '. $jwt
                ],200);

            }
            // Jika tidak ada user, maka buat user baru
            $newUser = User::create([
                'name' => $userFromGoogle->getName(),
                'email' => $userFromGoogle->getEmail(),
                'password' => bcrypt($userFromGoogle->getEmail())
            ]);

            $payload = [
                'name'=> $userFromGoogle->getEmail(),
                'role'=> 'user',
                'iat'=> now()->timestamp,//waktu token di generate
                'exp'=> now()->timestamp + 60*60*2 //waktu token expire

            ];
            //generate token
            $jwt = JWT::encode($payload,env('JWT_SECRET_KEY'),'HS256');
            //kirim token ke user
            return response()->json([
                'message'=>'Register Berhasil',
                'name'=>$userFromGoogle->getName(),
                'token'=>'Bearer '.$jwt
            ], 200);
        
        } catch (\Exception $e) {
            return redirect()->away('http://127.0.0.1:8000/api/oauth/register');
        }
    }   

}
