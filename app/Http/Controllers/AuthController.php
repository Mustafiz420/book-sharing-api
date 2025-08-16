<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\ResponseService;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly ResponseService $response
    ) {}

    public function register(RegisterUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $this->users->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'role' => 'user',
        ]);

        $token = $user->createToken('authToken')->accessToken;

        return $this->response->created([
            'user' => (new UserResource($user))->toArray($request),
            'token' => $token,
        ], 'User registered successfully');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return $this->response->error('Invalid credentials', 401);
        }

        /** @var User $user */
        $user = Auth::user();
        $token = $user->createToken('authToken')->accessToken;

        return $this->response->success([
            'token' => $token,
        ], 'Login successful');
    }

    public function logout(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $token = $user?->token();

        if ($token) {
            // Revoke access token
            $token->revoke();
            // Revoke linked refresh tokens
            DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $token->id)
                ->update(['revoked' => true]);
        }

        return $this->response->success([], 'Logged out successfully');
    }
}
