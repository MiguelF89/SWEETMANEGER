<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                "error" => "Credenciais inválidas"
            ], 401);
        }

        // Criar token Sanctum
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            "token" => $token,
            "user" => $user
        ]);
    }
}
