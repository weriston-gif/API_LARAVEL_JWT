<?php

namespace App\Services\Auth;

class LoginService
{
    public function execute(array $credentials): array
    {
        if (!$token = auth()->setTTL(6 * 60)->attempt($credentials)) {
            throw new \Exception('not authenticate', 401);
        }

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => auth()->user(),
        ];
    }
}
