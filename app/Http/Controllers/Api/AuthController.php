<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        $token = $user->createToken('sanctum');
        return response()->json([
            'status' => 200,
            'message' => 'Successfully Registerd',
            'access-token' => $token->plainTextToken,
        ]);
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = auth()->user();

            $token = $user->createToken('sanctum', ['user:show']);
            return response()->json([
                'status' => 200,
                'message' => 'Successfully Login',
                'access-token' => $token->plainTextToken,
            ]);
        }
    }

    public function profile(Request $request)
    {
        $user = auth()->user();
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $user,
        ]);
    }

    public function userList(Request $request)
    {
        if (!auth()->user()->tokenCan('user:list')) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized',
            ]);
        }
        $user = User::all();
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $user,
        ]);
    }
}
