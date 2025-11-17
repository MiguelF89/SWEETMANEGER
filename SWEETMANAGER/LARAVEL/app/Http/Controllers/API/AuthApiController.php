<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

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

        
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            "token" => $token,
            "user" => $user
        ]);
    }
}
