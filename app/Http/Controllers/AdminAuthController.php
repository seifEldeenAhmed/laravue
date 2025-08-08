<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Log;

class AdminAuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $admin = Admin::create($request->validated());

            return $this->responseWithSuccess([
                'user' => new UserResource($admin)
            ], 'Admin registered successfully', 201);
        } catch (Exception $e) {
            Log::error('Failed to register admin', [
                'error' => $e->getMessage(),
                'request_data' => $request->validated(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->responseWithError('Failed to register admin: ' . $e->getMessage(), 500);
        }
    }

    public function login(AdminLoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $admin = Admin::where('email', $credentials['email'])->first();

            if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
                return $this->responseWithUnauthorized('Invalid credentials');
            }

            $token = $admin->createToken('AdminToken')->plainTextToken;

            return $this->responseWithSuccess([
                'user' => new UserResource($admin),
                'auth' => [
                    'token' => $token,
                    'type' => 'Bearer',
                ]
            ], 'Login successful');
        } catch (Exception $e) {
            Log::error('Admin login failed', [
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
            $admin = auth()->user();
            if ($admin) {
                $admin->tokens()->delete();
                return $this->responseWithSuccess(null, 'Admin logged out successfully');
            }
            return $this->responseWithUnauthorized('No admin logged in');
        } catch (Exception $e) {
            Log::error('Admin logout failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->responseWithError('Logout failed: ' . $e->getMessage(), 500);
        }
    }
}
