<?php

namespace App\Http\Controllers\apk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\User;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class JWTAuthController extends ApiController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|between:2,100',
            'email' => 'required|email|unique:users|max:50',
            'password' => 'required|confirmed|string|min:6',
            'role' => 'required|integer'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Helper::passwd($request->password),
            'role_id' => $request->role,
            'token' => Str::random(32),
        ]);
        
        $message = 'Registrasi user berhasil';
        return $this->successResponse($user, $message, 201);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }
        
        $cekUser = DB::table('users')
                        ->join('roles', 'roles.id', '=', 'users.role_id')
                        ->where([
                            'users.email' => $request->email,
                            'users.is_active' => 1
                        ])
                        ->select(
                            'users.id',
                            'users.role_id',
                            'users.name',
                            'users.email',
                            'roles.level as level_user',
                            'roles.role as role' 
                            )->first();

        if (is_null($cekUser)) {
            $message = 'Akun tidak ditemukan/tidak aktif';
            return $this->errorResponse($message, 404);
        }

        if (! $token = Auth::guard('api')->attempt($validator->validated())) {
            $message = 'Email atau password tidak sesuai';
            return $this->errorResponse($message, 401);
        }
        
        return $this->createNewToken($token, $cekUser);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token, $user)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'data' => $user
        ]);
    }
}
