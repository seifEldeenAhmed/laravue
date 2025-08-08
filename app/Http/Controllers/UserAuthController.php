<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Log;

class UserAuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create($request->validated());

            return $this->responseWithSuccess([
                'user' => new UserResource($user)
            ], 'User registered successfully', 201);
        } catch (Exception $e) {
            Log::error('Failed to register user', [
                'error' => $e->getMessage(),
                'request_data' => $request->validated(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->responseWithError('Failed to register user: ' . $e->getMessage(), 500);
        }
    }

    public function login(AdminLoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $user = User::where('email', $credentials['email'])->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return $this->responseWithUnauthorized('Invalid credentials');
            }

            $token = $user->createToken('UserToken')->plainTextToken;

            return $this->responseWithSuccess([
                'user' => new UserResource($user),
                'auth' => [
                    'token' => $token,
                    'type' => 'Bearer',
                ]
            ], 'Login successful');
        } catch (Exception $e) {
            Log::error('User login failed', [
                'error' => $e->getMessage(),
                'email' => $request->input('email'),
                'ip_address' => request()->ip(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->responseWithError('Login failed: ' . $e->getMessage(), 500);
        }
    }

    public function logout()
    {
        try {
            $user = auth()->user();
            if ($user) {
                $user->tokens()->delete();
                return $this->responseWithSuccess(null, 'User logged out successfully');
            }
            return $this->responseWithUnauthorized('No user logged in');
        } catch (Exception $e) {
            Log::error('User logout failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->responseWithError('Logout failed: ' . $e->getMessage(), 500);
        }
    }
}
