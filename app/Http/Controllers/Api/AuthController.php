<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Login user and create token.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'errors' => [
                    'email' => ['The provided credentials are incorrect.'],
                ],
            ], 401);
        }

        try {
            $user = Auth::user();
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'token' => $token,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed. Please try again.',
                'error' => 'Internal server error',
            ], 500);
        }
    }

    /**
     * Logout user and revoke token.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'error' => 'No valid user found',
                ], 401);
            }
            
            $currentToken = $user->currentAccessToken();
            
            if ($currentToken) {
                $currentToken->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
                'data' => null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed. Please try again.',
                'error' => 'Internal server error',
                'debug' => [
                    'exception' => $e->getMessage(),
                ],
            ], 500);
        }
    }

    /**
     * Get authenticated user.
     */
    public function user(Request $request): JsonResponse
    {
        try {
            $bearerToken = $request->bearerToken();
            
            if (!$bearerToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bearer token is missing',
                    'error' => 'No token provided',
                    'debug' => [
                        'headers' => $request->headers->all(),
                        'authorization' => $request->header('Authorization'),
                    ],
                ], 401);
            }
            
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'error' => 'Invalid token or user not found',
                    'debug' => [
                        'token_present' => !empty($bearerToken),
                        'token_length' => strlen($bearerToken),
                        'token_preview' => substr($bearerToken, 0, 20) . '...',
                    ],
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user information',
                'error' => 'Internal server error',
                'debug' => [
                    'exception' => $e->getMessage(),
                ],
            ], 500);
        }
    }

    /**
     * Refresh user token.
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'error' => 'Unauthorized access',
                ], 401);
            }

            // Get the current access token and check if it exists
            $currentToken = $user->currentAccessToken();
            
            if ($currentToken) {
                // Delete the current access token
                $currentToken->delete();
            }
            
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'token' => $token,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh token',
                'error' => 'Internal server error',
                'debug' => [
                    'exception' => $e->getMessage(),
                ],
            ], 500);
        }
    }
}
