<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email:dns',
            'password' => 'required|min:8'
        ]);

        $user = User::where('email', $request->email)->first();
        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => true,
                'message' => 'Logged In',
                'data' => $user,
                'token' => $this->jwt($user)
            ], 200);
        }

        return response()->json([
            'status' => true,
            'message' => 'Email or Password is Invalid',
            'data' => '',
        ], 404);
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email:dns|unique:users',
            'password' => 'required|min:8'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa'
        ]);

        if ($user) {
            return response()->json([
                'status' => true,
                'message' => 'User created !',
                'data' => $user
            ], 201);
        }

        return response()->json([
            'status' => false,
            'message' => 'Error',
            'data' => ''
        ], 402);
    }

    public function me()
    {
        return response()->json([
            'status' => true,
            'message' => 'Profile',
            'data' => User::all()
        ], 200);
    }


    protected function jwt(User $user)
    {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 60 * 60 // Expiration time
        ];
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    public function tes()
    {
        return 'tes middleware';
    }
}
