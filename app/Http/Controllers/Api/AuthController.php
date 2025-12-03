<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Exception;
class AuthController extends Controller
{
      public function register(Request $request)
    {
        try {
            $request->validate([
                'name'=>'required',
                'email'=>'required|email|unique:users',
                'password'=>'required|min:6'
            ]);

            $user = User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>bcrypt($request->password),
                'refresh_token'=>Str::random(80),
                'refresh_token_expiry'=>now()->addDays(7)
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'user'=>$user,
                'access_token'=>$token,
                'refresh_token'=>$user->refresh_token
            ]);
        } catch(Exception $e){
            return response()->json(['message'=>$e->getMessage()],400);
        }
    }


    public function login(Request $request)
    {
        try {
            $request->validate(['email'=>'required|email','password'=>'required']);
            $user = User::where('email',$request->email)->first();

            if(!$user || !Hash::check($request->password,$user->password)){
                return response()->json(['message'=>'Invalid credentials'],401);
            }

            $token = JWTAuth::fromUser($user);
            $refreshToken = Str::random(80);
            $user->refresh_token = $refreshToken;
            $user->refresh_token_expiry = now()->addDays(7);
            $user->save();

            return response()->json([
                'user'=>$user,
                'access_token'=>$token,
                'refresh_token'=>$refreshToken
            ]);
        } catch(Exception $e){
            return response()->json(['message'=>$e->getMessage()],400);
        }
    }


    // public function loginWithGoogle(Request $request)
    // {
    //     try {
    //         $request->validate(['token'=>'required']);
    //         $googleUser = $this->verifyGoogleToken($request->token);
    //         if(!$googleUser) return response()->json(['message'=>'Invalid Google token'],401);

    //         $user = User::firstOrCreate(
    //             ['oauth_provider'=>'google','oauth_id'=>$googleUser['sub']],
    //             [
    //                 'name'=>$googleUser['name'],
    //                 'email'=>$googleUser['email'] ?? null,
    //                 'avatar_url'=>$googleUser['picture'] ?? null,
    //                 'password'=>null,
    //                 'refresh_token'=>Str::random(80),
    //                 'refresh_token_expiry'=>now()->addDays(7)
    //             ]
    //         );


    //         $user->refresh_token = Str::random(80);
    //         $user->refresh_token_expiry = now()->addDays(7);
    //         $user->save();

    //         $token = JWTAuth::fromUser($user);

    //         return response()->json([
    //             'user'=>$user,
    //             'access_token'=>$token,
    //             'refresh_token'=>$user->refresh_token
    //         ]);
    //     } catch(Exception $e){
    //         return response()->json(['message'=>$e->getMessage()],400);
    //     }
    // }


    public function refresh(Request $request)
    {
        try {
            $request->validate(['refresh_token'=>'required']);
            $user = User::where('refresh_token',$request->refresh_token)
                        ->where('refresh_token_expiry','>',now())
                        ->first();
                // dd($user);
            if(!$user) return response()->json(['message'=>'Invalid or expired refresh token'],401);
// dd('smpe sni');
            $token = JWTAuth::fromUser($user);
            $refreshToken = Str::random(80);
            $user->refresh_token = $refreshToken;
            $user->refresh_token_expiry = now()->addDays(7);
            $user->save();

            return response()->json([
                'access_token'=>$token,
                'refresh_token'=>$refreshToken
            ]);
        } catch(Exception $e){
            return response()->json(['message'=>$e->getMessage()],400);
        }
    }

    // Logout
    public function logout(Request $request)
    {
        try {
            $user = auth()->user();
            // dd($user);
            $user->refresh_token=null;
            $user->refresh_token_expiry=null;
            $user->save();
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json(['message'=>'Logged out']);
        } catch(Exception $e){
            return response()->json(['message'=>$e->getMessage()],400);
        }
    }

    // private function verifyGoogleToken($idToken)
    // {
    //     $clientId = env('GOOGLE_CLIENT_ID');
    //     $url = "https://oauth2.googleapis.com/tokeninfo?id_token={$idToken}";
    //     $data = json_decode(file_get_contents($url),true);
    //     if(isset($data['aud']) && $data['aud']===$clientId) return $data;
    //     return null;
    // }
}
