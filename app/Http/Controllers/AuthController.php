<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = [
            'nickname' => $request->nickname,
            'password' => $request->password
        ];
        if (Auth::attempt($credentials)) {
            $token = Auth::user()->createToken($request->nickname)->plainTextToken;
            session()->put('token', $token);
            return response()->json([
                "status" => 1,
                "msg" => "Logeado correctamente",
                "token" => $token
            ]);
        }
        return response()->json([
            "msg" => "Usuario y/o contraseña inválido"
        ], 422);
    }

    public function userProfile() {
        return response()->json([
            "status" => 0,
            "msg" => "Acerca del perfil de usuario",
            "data" => auth()->user()
        ]);
    }

    public function logout() {
        auth()->user()->tokens()->delete();
        return response()->json([
            "status" => 1,
            "msg" => "Cierre de sesión",
        ]);
    }

}
