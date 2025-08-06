<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $credentials['role'] = 'admin'; // Hanya login admin

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email or password',
                'errors' => [
                    'email' => [
                        'The email or password is invalid!'
                    ]
                ]
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'type' => 'bearer',
            'user' => Auth::guard('api')->user(),
        ]);
    }
}
