<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Requests\Api\StoreUserRequest;

class AuthController extends Controller
{
    public function register(StoreUserRequest $request){
        $validated = $request->validated();

        User::create($validated);

        return response()->json([
            'status' => 'success',
            "message" => "User registration successful"
        ],Response::HTTP_CREATED);
    }

    public function login(LoginUserRequest $request){
        $validated = $request->validated();

        if(!Auth::attempt($validated)){
            return response()->json([
                'status' => 'error',
                "message" => "login credentials not match"
            ],Response::HTTP_UNAUTHORIZED);
        }
        $tokenName = env('TOKEN_NAME', 'token');
        $token = $request->user()->createToken($tokenName);

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully loggedIn',
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer'
        ]);
    }

    public function logout(Request $request){
        $request->user()->logout;
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'logout success'
        ]);
    }
}
