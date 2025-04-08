<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Hash;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * register 
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string',
            'email'=>'required|string|email|unique:users,email',
            'password'=>'required|string|min:8',
        ]);

        $user = User::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'password'=>Hash::make($data['password'])
        ]);
        $token = $user->createToken('mytoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response()->json($response,201);
    }

    /**
     *  login 
     */
    public function login(Request $request)
    {   
        $request->validate([
            'email'=>'required|string|email',
            'password'=>'required|string|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => ['These credentials do not match records.']
            ], 404);
        }

        $token = $user->createToken('mytoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response()->json($response,201);
    }

    /**
     * logout 
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        $response = [
            'message'=>'logged out successfully',
        ];
        return response($response);
    }
    
}
