<?php

namespace App\Http\Controllers\Api\Auth;

use App\Customs\Services\EmailVerificationservice;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private EmailVerificationService $service){}

    public function login(LoginRequest $request)
    {
        $token = auth()->attempt($request->validated());
        if($token){
            return $this->responseWithToken($token, auth()->user());
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Credenciais inválidas'
            ], 401);
        }
    }

    public function register(RegistrationRequest $request)
    {
        $user = User::create($request->validated());
        if ($user) {
            $this->service->sendVerificationLink($user);
            $token = auth()->login($user);
            return $this->responseWithToken($token, $user);
        }else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Ocorreu um erro ao tentar criar um utilizador'
            ], 500);
        }
    }

    /**
     * Return JWT access token
     */

     public function responseWithToken($token, $user)
     {
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'access_token' => $token,
            'type' => 'bearer'
        ]);
     }


}

