<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\V1\LoginUserRequest;
use App\Http\Requests\Api\V1\StoreUserRequest;

class AuthController extends Controller
{
    public function register(StoreUserRequest $request){
        $validated = $request->validated();

        User::create($validated);

        return response()->json([
            "message" => "registration success"
        ],Response::HTTP_CREATED);
    }

    public function login(LoginUserRequest $request){
        $validated = $request->validated();

        if(!Auth::attempt($validated)){
            return response()->json([
                "message" => "login credentials not match"
            ],Response::HTTP_UNAUTHORIZED);
        }

        $token = $request->user()->createToken("token");

        return response()->json([
            'message' => 'login success',
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer'
        ]);
    }

    public function logout(Request $request){
        $request->user()->logout;
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'logout success'
        ]);
    }
}
