<?php

namespace App\Services;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RoleRepositoryInterface $roleRepository,
    ) {}

    public function register(array $data): array
    {
        $role = $this->roleRepository->findBySlug($data['role'] ?? 'customer');

        if (! $role) {
            throw ValidationException::withMessages(['role' => ['Invalid role specified.']]);
        }

        $user = $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $token = JWTAuth::fromUser($user);

        return $this->buildTokenResponse($user, $token);
    }

    public function login(array $credentials): array
    {
        if (! $token = JWTAuth::attempt($credentials)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        /** @var User $user */
        $user = auth()->user();

        if (! $user->is_active) {
            JWTAuth::invalidate($token);
            throw new AuthenticationException('Account is deactivated.');
        }

        return $this->buildTokenResponse($user->load('role.permissions'), $token);
    }

    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    public function refresh(): array
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());
        /** @var User $user */
        $user = auth()->user()->load('role.permissions');

        return $this->buildTokenResponse($user, $token);
    }

    public function me(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->load('role.permissions');
    }

    private function buildTokenResponse(User $user, string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => $user,
        ];
    }
}
