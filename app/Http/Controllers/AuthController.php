<?php

namespace App\Http\Controllers;

use App\Models\ResetPassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

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
            'role' => $request->role
        ]);
        $user->assignRole($request->role);

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

    public function sendmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email:dns'
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $kode = Str::random(32);
            $data = ["code" => $kode];
            $cek = ResetPassword::where('email', $user->email)->first();
            if ($cek) {
                $cek->token = $kode;
                $cek->save();
                Mail::send('mail', $data, function ($message) use ($user) {
                    $message->to($user->email, 'Programmer Lokal')->subject('Tes email');
                    $message->from('achmadfawait66@gmail.com', 'Programmer Lokal');
                });
                return response()->json([
                    'status' => true,
                    'message' => 'Please check email for you get code'
                ], 200);
            }
            ResetPassword::create([
                'email' => $user->email,
                'token' => $kode
            ]);
            Mail::send('mail', $data, function ($message) use ($user) {
                $message->to($user->email, 'Programmer Lokal')->subject('Kode Verifikasi Reset Password');
                $message->from('achmadfawait66@gmail.com', 'Programmer Lokal');
            });
            return response()->json([
                'status' => true,
                'message' => 'Please check email for you get code'
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'Email Un Registered'
        ], 404);
    }

    public function reset(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email:dns',
            'password' => 'required|min:8'
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $reset = ResetPassword::where('email', $user->email)->first();
            if ($user->email == $reset->email && $reset->token == $request->header('token')) {
                $user->password = Hash::make($request->password);
                $user->save();
                return response()->json([
                    'status' => true,
                    'message' => 'Password has been reset, Please login'
                ], 200);
            }
            return response()->json([
                'status' => false,
                'message' => 'Email or token is invalid'
            ], 404);
        }
        return response()->json([
            'status' => false,
            'message' => 'Email Un Registered'
        ], 404);
    }
}
