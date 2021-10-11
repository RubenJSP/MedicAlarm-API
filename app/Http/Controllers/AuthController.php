<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class AuthController extends Controller
{
     /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials)){
            return response()->json([
                'message' => 'No Autorizado',
            ], 401);
        }

        $user = $request->user();
        $token = $this->createToken($user,$request->remember_me);

        return $token;
    }

    public function createToken($user,$remember_me){
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if($remember_me != null && !$remember_me)
            $token->expires_at = Carbon::now()->addDays(30);
        $token->save();
        
        $role = User::where('email',$user->email)->first();
        $role = $role->getRoleNames()[0];
        $user = Auth::user();
        if($role != "Medic")
            $user->makeHidden(['professional_id','hospital','speciality',]);

        return response()->json([
            'user' => $user,
            'role' => $role,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ],200);
    }

    public function checkToken(Request $request){
        if($request->user() != null){
            $revoked = $request->user()->token()->revoked;
            $expiration = $request->user()->token()->expires_at;

            if(new Carbon($expiration) > Carbon::now() && !$revoked){
                return response()->json([
                    'message' => 'El token está vigente',
                ],200);
            }
        }

        return response()->json([
            'message' => 'El token es incorrecto o ha expirado',
        ],401);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Sesión finalizada'
        ],200);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
